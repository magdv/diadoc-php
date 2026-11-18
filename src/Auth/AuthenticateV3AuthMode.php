<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

use Diadoc\Proto\LoginPassword;
use Exception;
use MagDv\Diadoc\Entities\Http\HttpTimeouts;
use MagDv\Diadoc\Interfaces\HttpTimeoutsAwareInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Устаревший способ авторизации DiadocAuth (ddauth_api_client_id + ddauth_token).
 *
 * Используется по умолчанию для обратной совместимости: поведение полностью
 * повторяет исходный DiadocApi до внедрения OIDC.
 *
 * Возможности:
 *  - токен может быть установлен извне (setToken / authenticateLoginV3 в DiadocApi);
 *  - при переданном кеше токен восстанавливается в auth() и сохраняется при установке;
 *  - при переданных login/password и serviceUrl режим сам получает токен
 *    через /V3/Authenticate (auth()) и перелогинивается при 401 (handleUnauthorized).
 */
class AuthenticateV3AuthMode implements AuthModeInterface, HttpTimeoutsAwareInterface
{
    use TokenSanitizerTrait;

    public const MODE = 'authenticate_v3';

    /**
     * TTL хранения ddauth-токена в кеше (сек): 24 часа.
     */
    private const CACHE_TTL = 24 * 3600;

    /**
     * Таймаут HTTP-запроса (сек), действовавший до появления HttpTimeouts.
     */
    private const LEGACY_TIMEOUT = 20;

    private ?string $token = null;

    private ?HttpTimeouts $timeouts = null;

    public function __construct(
        private readonly string $ddauthApiClientId,
        private readonly ?CacheInterface $cache = null,
        private readonly ?string $cacheKey = null,
        private readonly ?string $login = null,
        private readonly ?string $password = null,
        private readonly ?string $serviceUrl = null,
        ?HttpTimeouts $timeouts = null,
    ) {
        $this->timeouts = $timeouts;
    }

    public function getMode(): string
    {
        return self::MODE;
    }

    public function setHttpTimeouts(?HttpTimeouts $timeouts): void
    {
        $this->timeouts = $timeouts;
    }

    public function getHttpTimeouts(): ?HttpTimeouts
    {
        return $this->timeouts;
    }

    public function buildRequestHeaders(?string $contentType, string $method, bool $forAuthenticateCall): array
    {
        $clientId = $this->sanitizeForHttpHeader($this->ddauthApiClientId);

        if ($forAuthenticateCall) {
            $lines = ['Authorization: DiadocAuth ddauth_api_client_id=' . $clientId];
        } else {
            $token = $this->getToken();
            if ($token === null || $token === '') {
                throw new Exception('Нет ddauth_token: выполните authenticateLoginV3() или setLegacyToken()');
            }

            $lines = [
                'Authorization: DiadocAuth ddauth_api_client_id=' . $clientId
                . ',ddauth_token=' . $this->sanitizeForHttpHeader($token),
            ];
        }

        if ($method === 'POST') {
            $lines[] = 'Content-Type: ' . ($contentType ?: 'application/x-protobuf');
        } else {
            $lines[] = 'Accept: application/x-protobuf';
            if ($contentType !== null && $contentType !== '') {
                $lines[] = 'Content-Type: ' . $contentType;
            }
        }

        return $lines;
    }

    public function ensureAuthorized(): void
    {
        if ($this->token !== null && $this->token !== '') {
            return;
        }

        $this->restoreTokenFromCache();

        if (($this->token === null || $this->token === '') && $this->canAuthenticate()) {
            $this->authenticate();
        }

        if ($this->getToken() === null || $this->getToken() === '') {
            throw new Exception('Unauthorized request: нет ddauth_token (authenticate_v3)');
        }
    }

    public function handleUnauthorized(): bool
    {
        // В legacy-режиме нет refresh-токена — повторять запрос не нужно.
        return false;
    }

    public function setToken(?string $token): void
    {
        if ($token === null || $token === '') {
            $this->token = null;
            $this->forgetCachedToken();

            return;
        }

        $this->token = $this->sanitizeForHttpHeader($this->normalizeToken($token));
        if ($this->token === '') {
            $this->token = null;
            $this->forgetCachedToken();

            return;
        }

        $this->persistToken($this->token);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    private function canAuthenticate(): bool
    {
        return $this->login !== null
            && $this->login !== ''
            && $this->password !== null
            && $this->password !== ''
            && $this->serviceUrl !== null
            && $this->serviceUrl !== '';
    }

    /**
     * Получить ddauth-токен через /V3/Authenticate (логин/пароль).
     *
     * @throws Exception
     */
    private function authenticate(): void
    {
        $loginPassword = new LoginPassword();
        $loginPassword->setLogin($this->login);
        $loginPassword->setPassword($this->password);

        $token = $this->doAuthenticateRequest(
            sprintf('%s/V3/Authenticate?type=password', rtrim((string) $this->serviceUrl, '/')),
            $loginPassword->serializeToString(),
        );

        $this->setToken($token);
    }

    /**
     * @throws Exception
     */
    private function doAuthenticateRequest(string $uri, string $body): string
    {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $timeoutOptions = [CURLOPT_TIMEOUT => self::LEGACY_TIMEOUT];
        if ($this->timeouts !== null) {
            $timeoutOptions = $this->timeouts->toCurlOptions();
        }
        curl_setopt_array($ch, $timeoutOptions);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildRequestHeaders('application/x-protobuf', 'POST', true));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch) !== 0) {
            $err = sprintf('Authenticate curl error: (%s) %s', curl_errno($ch), curl_error($ch));
            curl_close($ch);
            throw new Exception($err);
        }

        curl_close($ch);
        if ($response === false || $httpCode !== 200) {
            throw new Exception(sprintf('Authenticate error http code: (%s) %s', $httpCode, $response));
        }

        return (string) $response;
    }

    private function restoreTokenFromCache(): void
    {
        if (!$this->cache instanceof CacheInterface || $this->cacheKey === null) {
            return;
        }

        $token = $this->cache->get($this->cacheKey);
        if (is_string($token) && $token !== '') {
            $this->token = $this->sanitizeForHttpHeader($this->normalizeToken($token));
            if ($this->token === '') {
                $this->token = null;
            }
        }
    }

    private function persistToken(string $token): void
    {
        if (!$this->cache instanceof CacheInterface || $this->cacheKey === null) {
            return;
        }

        try {
            $this->cache->set($this->cacheKey, $token, self::CACHE_TTL);
        } catch (\Throwable) {
            // кеш недоступен — авторизация всё равно работает в рамках процесса
        }
    }

    private function forgetCachedToken(): void
    {
        if (!$this->cache instanceof CacheInterface || $this->cacheKey === null) {
            return;
        }

        try {
            $this->cache->delete($this->cacheKey);
        } catch (\Throwable) {
            // кеш недоступен — не критично
        }
    }
}
