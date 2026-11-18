<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

use Exception;
use MagDv\Diadoc\Entities\Http\HttpTimeouts;
use MagDv\Diadoc\Exception\DiadocApiException;
use MagDv\Diadoc\Interfaces\HttpTimeoutsAwareInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * OpenID Connect авторизация (Authorization Code + Bearer).
 *
 * Реализует:
 *  - построение URL авторизации и обмен authorization code на токены;
 *  - проактивное обновление access_token по refresh_token;
 *  - один ретрай запроса при 401 с refresh;
 *  - определение scope (Diadoc.PublicAPI / Diadoc.PublicAPI.Staging) по окружению;
 *  - восстановление/сохранение OAuth-сессии во внешнем кеше (если передан).
 */
class OidcAuthFlowAuthMode implements AuthModeInterface, HttpTimeoutsAwareInterface
{
    use TokenSanitizerTrait;

    public const MODE = 'oidc_auth_flow';

    private const IDENTITY_PATH_AUTHORIZE = '/connect/authorize';

    private const IDENTITY_PATH_TOKEN = '/connect/token';

    /**
     * TTL хранения OAuth-сессии в кеше (сек): 30 дней (ориентир на срок refresh_token).
     */
    private const CACHE_TTL = 30 * 24 * 3600;

    /**
     * Таймауты HTTP-запросов к identity-серверу до появления HttpTimeouts.
     */
    private const LEGACY_CONNECT_TIMEOUT = 15;

    private const LEGACY_TIMEOUT = 30;

    private ?string $token = null;

    private ?string $refreshToken = null;

    private ?int $accessTokenExpiresAt = null;

    private string $oidcScope;

    private ?HttpTimeouts $timeouts = null;

    /**
     * @var callable|null function (array): void — для сохранения access/refresh после обновления
     */
    private $oauthSessionPersistenceCallback;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $identityBaseUrl,
        /**
         * Базовый URL API Диадока, по которому резолвится scope.
         *
         * Публичный readonly намеренно: DiadocApi::create() сверяет его со своим
         * serviceUrl (см. {@see \MagDv\Diadoc\DiadocApi::create()}), чтобы исключить
         * рассинхронизацию при создании клиента.
         */
        public readonly string $serviceUrl,
        private readonly ?CacheInterface $cache = null,
        private readonly ?string $cacheKey = null,
        ?HttpTimeouts $timeouts = null,
    ) {
        $this->timeouts = $timeouts;
        $this->oidcScope = $this->resolveOidcScope($serviceUrl);

        if ($this->cache instanceof CacheInterface && $this->cacheKey !== null) {
            $this->setOAuthSessionPersistenceCallback(
                function (array $session): void {
                    $this->persistSession($session);
                }
            );
        }
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

    /**
     * @return array<int, string>
     */
    public function buildRequestHeaders(?string $contentType, string $method, bool $forAuthenticateCall): array
    {
        $token = $this->getToken();
        if ($token === null || $token === '') {
            throw new Exception('Нет access_token: выполните exchangeAuthorizationCode или setOAuthSession/setToken');
        }

        $lines = ['Authorization: Bearer ' . $this->sanitizeForHttpHeader($token)];

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

        if (!$this->cache instanceof CacheInterface || $this->cacheKey === null) {
            return;
        }

        $session = $this->cache->get($this->cacheKey);
        if (\is_array($session)
        && isset($session['access_token'])
        && \is_string($session['access_token'])
        && $session['access_token'] !== ''
        ) {
            $this->setOAuthSession(
                $session['access_token'],
                isset($session['refresh_token']) && \is_string($session['refresh_token']) ? $session['refresh_token'] : null,
                isset($session['expires_at']) && \is_int($session['expires_at']) ? $session['expires_at'] : null,
            );
        }

        $this->ensureAccessTokenFresh();

        if ($this->getToken() === null || $this->getToken() === '') {
            throw new Exception('Unauthorized request: нет access_token (OIDC)');
        }
    }

    public function handleUnauthorized(): bool
    {
        if ($this->refreshToken === null || $this->refreshToken === '') {
            return false;
        }

        $this->refreshAccessToken();
        $this->ensureAuthorized();

        return true;
    }

    public function setToken(?string $token): void
    {
        if ($token === null || $token === '') {
            $this->token = null;
            $this->refreshToken = null;
            $this->accessTokenExpiresAt = null;
            $this->forgetCachedToken();

            return;
        }

        $this->token = $this->sanitizeForHttpHeader($this->normalizeToken($token));
        if ($this->token === '') {
            $this->token = null;
            $this->refreshToken = null;
            $this->accessTokenExpiresAt = null;

            return;
        }

        // Только access: отключаем проактивный refresh (нет срока и refresh в связке)
        $this->refreshToken = null;
        $this->accessTokenExpiresAt = null;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Колбэк вызывается после обмена кода, refresh и при {@see setOAuthSession}, чтобы сохранить сессию (файл, БД).
     *
     * @param callable|null $callback function (array $session): void
     *        $session = ['access_token' => string, 'refresh_token' => ?string, 'expires_at' => ?int]
     */
    public function setOAuthSessionPersistenceCallback(?callable $callback): void
    {
        $this->oauthSessionPersistenceCallback = $callback;
    }

    /**
     * URL редиректа на страницу авторизации Контур (Authorization Code Flow).
     *
     * @see https://developer.kontur.ru/docs/diadoc-api/authentication.html
     */
    public function buildAuthorizationUrl(string $redirectUri, string $state, ?string $nonce = null): string
    {
        if ($nonce === null || $nonce === '') {
            $nonce = $this->randomUrlSafeString(16);
        }

        $encType = \defined('PHP_QUERY_RFC3986') ? PHP_QUERY_RFC3986 : PHP_QUERY_RFC1738;
        $query = \http_build_query(
            [
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'scope' => $this->oidcScope,
                'redirect_uri' => $redirectUri,
                'state' => $state,
                'nonce' => $nonce,
            ],
            '',
            '&',
            $encType
        );

        return $this->identityUri(self::IDENTITY_PATH_AUTHORIZE) . '?' . $query;
    }

    /**
     * Обмен authorization code на access/refresh токены.
     *
     * @throws DiadocApiException
     */
    public function exchangeAuthorizationCode(string $code, string $redirectUri): void
    {
        $encType = \defined('PHP_QUERY_RFC3986') ? PHP_QUERY_RFC3986 : PHP_QUERY_RFC1738;
        $body = \http_build_query(
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $redirectUri,
            ],
            '',
            '&',
            $encType
        );
        $data = $this->requestIdentityToken($body);
        $this->applyTokenResponse($data);
    }

    /**
     * Обновление access_token по refresh_token.
     *
     * @throws DiadocApiException
     */
    public function refreshAccessToken(): void
    {
        if ($this->refreshToken === null || $this->refreshToken === '') {
            throw new DiadocApiException('refresh_token отсутствует: пройдите Authorization Code Flow заново', 0);
        }

        $encType = \defined('PHP_QUERY_RFC3986') ? PHP_QUERY_RFC3986 : PHP_QUERY_RFC1738;
        $body = \http_build_query(
            [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
            ],
            '',
            '&',
            $encType
        );
        $data = $this->requestIdentityToken($body);
        $this->applyTokenResponse($data);
    }

    /**
     * Установить токены вручную (например из кеша после перезапуска).
     *
     * @param int|null $expiresAtUnix время истечения access_token (unix), null — неизвестно (без проактивного refresh)
     */
    public function setOAuthSession(string $accessToken, ?string $refreshToken = null, ?int $expiresAtUnix = null): void
    {
        $t = $this->sanitizeForHttpHeader($this->normalizeToken($accessToken));
        $this->token = $t !== '' ? $t : null;
        $this->refreshToken = $refreshToken !== null && $refreshToken !== '' ? $this->sanitizeForHttpHeader($refreshToken) : null;
        $this->accessTokenExpiresAt = $expiresAtUnix;
        $this->notifyOAuthSessionPersistence();
    }

    /**
     * @return array{access_token: string, refresh_token: ?string, expires_at: ?int}
     */
    public function getOAuthSessionState(): array
    {
        $access = $this->getToken();

        return [
            'access_token' => $access ?? '',
            'refresh_token' => $this->refreshToken,
            'expires_at' => $this->accessTokenExpiresAt,
        ];
    }

    public function getOidcScope(): string
    {
        return $this->oidcScope;
    }

    private function resolveOidcScope(string $serviceUrl): string
    {
        $base = 'openid profile email offline_access ';
        $host = (string) \parse_url($serviceUrl, PHP_URL_HOST);
        $isStaging = \stripos($serviceUrl, 'staging') !== false
            || \stripos($host, 'diadoc-api-staging') !== false
            || \stripos($serviceUrl, 'diadoc-api-test') !== false
            || \stripos($host, 'diadoc-api-test') !== false;

        return $base . ($isStaging ? 'Diadoc.PublicAPI.Staging' : 'Diadoc.PublicAPI');
    }

    private function randomUrlSafeString(int $byteLength): string
    {
        $raw = \random_bytes(\max(1, $byteLength));

        return \rtrim(\strtr(\base64_encode($raw), '+/', '-_'), '=');
    }

    /**
     * Полный URI identity-сервера без задвоения слэша (DIADOC_IDENTITY_URL может заканчиваться на '/').
     */
    private function identityUri(string $path): string
    {
        return \rtrim($this->identityBaseUrl, '/') . $path;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function requestIdentityToken(string $body): array
    {
        $uri = $this->identityUri(self::IDENTITY_PATH_TOKEN);
        $ch = \curl_init($uri);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $timeoutOptions = [
            \CURLOPT_CONNECTTIMEOUT => self::LEGACY_CONNECT_TIMEOUT,
            \CURLOPT_TIMEOUT => self::LEGACY_TIMEOUT,
        ];
        if ($this->timeouts !== null) {
            $timeoutOptions = $this->timeouts->toCurlOptions();
        }
        \curl_setopt_array($ch, $timeoutOptions);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 64);
        if (\defined('CURL_HTTP_VERSION_1_1')) {
            \curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }

        \curl_setopt($ch, CURLOPT_POST, 1);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ];
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = \curl_exec($ch);
        $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (\curl_errno($ch) !== 0) {
            $err = \sprintf('Identity curl error: (%s) %s', \curl_errno($ch), \curl_error($ch));
            \curl_close($ch);
            throw new DiadocApiException($err, \curl_errno($ch));
        }

        \curl_close($ch);
        if ($response === false) {
            throw new DiadocApiException('Identity token request failed', 0);
        }

        $data = \json_decode((string) $response, true);
        if (! \is_array($data)) {
            throw new DiadocApiException('Identity token response is not JSON: ' . \substr((string) $response, 0, 500), (int) $httpCode);
        }

        if ($httpCode !== 200) {
            $msg = match (true) {
                isset($data['error_description']) && \is_string($data['error_description']) => $data['error_description'],
                isset($data['error']) && \is_string($data['error']) => $data['error'],
                default => 'token error',
            };

            throw new DiadocApiException('Identity /connect/token: ' . $msg, (int) $httpCode);
        }

        return $data;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function applyTokenResponse(array $data): void
    {
        if (empty($data['access_token']) || ! \is_string($data['access_token'])) {
            throw new DiadocApiException('Identity token response без access_token', 0);
        }

        $access = $this->sanitizeForHttpHeader($this->normalizeToken($data['access_token']));
        $newRefresh = match (true) {
            isset($data['refresh_token']) && \is_string($data['refresh_token']) && $data['refresh_token'] !== '' => $this->sanitizeForHttpHeader($data['refresh_token']),
            $this->refreshToken !== null => $this->refreshToken,
            default => null,
        };

        $expiresAt = null;
        if (isset($data['expires_in'])) {
            $expiresIn = (int) $data['expires_in'];
            if ($expiresIn > 0) {
                $expiresAt = \time() + $expiresIn;
            }
        }

        $this->token = $access !== '' ? $access : null;
        $this->refreshToken = $newRefresh;
        $this->accessTokenExpiresAt = $expiresAt;
        $this->notifyOAuthSessionPersistence();
    }

    private function notifyOAuthSessionPersistence(): void
    {
        if ($this->oauthSessionPersistenceCallback === null) {
            return;
        }

        \call_user_func($this->oauthSessionPersistenceCallback, $this->getOAuthSessionState());
    }

    /**
     * @param array{access_token: string, refresh_token: ?string, expires_at: ?int} $session
     */
    private function persistSession(array $session): void
    {
        if (!$this->cache instanceof CacheInterface || $this->cacheKey === null) {
            return;
        }

        try {
            $this->cache->set($this->cacheKey, $session, self::CACHE_TTL);
        } catch (\Throwable) {
            // кеш недоступен — авторизация всё равно работает в рамках процесса
        }
    }

    /**
     * Проактивное обновление access_token по refresh до истечения срока.
     */
    private function ensureAccessTokenFresh(int $leewaySeconds = 120): void
    {
        match (true) {
            $this->refreshToken === null,
            $this->refreshToken === '',
            $this->accessTokenExpiresAt === null,
            \time() + $leewaySeconds < $this->accessTokenExpiresAt => null,
            default => $this->refreshAccessToken(),
        };
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
