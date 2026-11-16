<?php

declare(strict_types=1);

namespace MagDv\Diadoc\Auth;

use MagDv\Diadoc\Exception\DiadocApiException;
use Psr\SimpleCache\CacheInterface;

/**
 * OpenID Connect Device Authorization Flow (RFC 8628) для API Диадока.
 *
 * Реализует контракт {@see AuthModeInterface}, поэтому совместим с DiadocApi::create().
 *
 * Сценарий:
 *  1. startDeviceAuthorization() — получить device_code/user_code/verification_uri;
 *  2. пользователь открывает verification_uri в браузере и вводит user_code;
 *  3. awaitAuthorization() — опрос /connect/token до получения токенов;
 *  4. дальше access_token обновляется по refresh_token бесшовно
 *     (проактивно до истечения и ретраем при 401).
 *
 * При переданном кеше ({@see \Psr\SimpleCache\CacheInterface}) режим сам
 * восстанавливает OAuth-сессию в auth() и сохраняет её при каждом обновлении токенов.
 */
class OidcDeviceFlowAuthMode implements AuthModeInterface
{
    use TokenSanitizerTrait;

    public const MODE = 'oidc_device_flow';

    private const string IDENTITY_PATH_DEVICE_AUTHORIZATION = '/connect/deviceauthorization';

    private const string IDENTITY_PATH_TOKEN = '/connect/token';

    private const string GRANT_TYPE_DEVICE_CODE = 'urn:ietf:params:oauth:grant-type:device_code';

    /**
     * Запас времени (сек) до истечения access_token для проактивного refresh.
     */
    private const int REFRESH_LEEWAY_SECONDS = 120;

    /**
     * Дополнительная пауза (сек) при ответе slow_down.
     */
    private const int SLOW_DOWN_EXTRA_SECONDS = 5;

    /**
     * TTL хранения OAuth-сессии в кеше (сек): 30 дней (ориентир на срок refresh_token).
     */
    private const CACHE_TTL = 30 * 24 * 3600;

    private ?string $token = null;

    private ?string $refreshToken = null;

    private ?int $accessTokenExpiresAt = null;

    private string $oidcScope;

    /**
     * @var callable|null function (array): void — сохранение access/refresh после обновления
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
    ) {
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

    /**
     * Запрос начала Device Flow: POST /connect/deviceauthorization.
     *
     * @throws DiadocApiException
     */
    public function startDeviceAuthorization(): DeviceFlowStart
    {
        $body = $this->buildFormBody(
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->oidcScope,
            ]
        );

        $data = $this->requestIdentity($this->identityUri(self::IDENTITY_PATH_DEVICE_AUTHORIZATION), $body);

        foreach (['device_code', 'user_code', 'verification_uri', 'interval', 'expires_in'] as $required) {
            if (empty($data[$required])) {
                throw new DiadocApiException(
                    \sprintf('Identity /connect/deviceauthorization: в ответе нет "%s": %s', $required, $this->shortDump($data)),
                    0
                );
            }
        }

        return new DeviceFlowStart(
            deviceCode: (string) $data['device_code'],
            userCode: (string) $data['user_code'],
            verificationUri: (string) $data['verification_uri'],
            verificationUriComplete: isset($data['verification_uri_complete']) && \is_string($data['verification_uri_complete']) ? $data['verification_uri_complete'] : null,
            interval: (int) $data['interval'],
            expiresIn: (int) $data['expires_in'],
        );
    }

    /**
     * Один опрос /connect/token с grant_type=device_code.
     *
     * @throws DiadocApiException при сетевых/протокольных ошибках (не при pending/slow_down)
     */
    public function pollAccessToken(string $deviceCode): DeviceFlowPollResult
    {
        $body = $this->buildFormBody(
            [
                'grant_type' => self::GRANT_TYPE_DEVICE_CODE,
                'device_code' => $deviceCode,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->oidcScope,
            ]
        );

        $data = $this->requestIdentity($this->identityUri(self::IDENTITY_PATH_TOKEN), $body, allowErrorBody: true);

        if (isset($data['access_token']) && \is_string($data['access_token']) && $data['access_token'] !== '') {
            $expiresIn = isset($data['expires_in']) && (int) $data['expires_in'] > 0 ? (int) $data['expires_in'] : null;
            $refresh = isset($data['refresh_token']) && \is_string($data['refresh_token']) && $data['refresh_token'] !== '' ? $data['refresh_token'] : null;

            return DeviceFlowPollResult::success($data['access_token'], $refresh, $expiresIn);
        }

        $error = isset($data['error']) && \is_string($data['error']) ? $data['error'] : null;
        $description = isset($data['error_description']) && \is_string($data['error_description']) ? $data['error_description'] : null;

        return match ($error) {
            'authorization_pending' => DeviceFlowPollResult::pending(),
            'slow_down' => DeviceFlowPollResult::slowDown(),
            'expired_token' => DeviceFlowPollResult::failed(DeviceFlowPollStatus::ExpiredToken, $description),
            'access_denied' => DeviceFlowPollResult::failed(DeviceFlowPollStatus::AccessDenied, $description),
            default => throw new DiadocApiException(
                \sprintf('Identity /connect/token: неожиданный ответ: %s', $this->shortDump($data)),
                0
            ),
        };
    }

    /**
     * Обмен device_code на токены с ожиданием подтверждения пользователя.
     *
     * Блокирует вызов до получения токенов, истечения device_code или отказа пользователя.
     *
     * @param callable|null $onPoll function (int $intervalSeconds): void — уведомление перед каждой паузой
     *
     * @throws DiadocApiException
     */
    public function awaitAuthorization(string $deviceCode, int $intervalSeconds, int $expiresIn, ?callable $onPoll = null): void
    {
        $deadline = \time() + $expiresIn;
        $interval = \max(1, $intervalSeconds);

        while (\time() <= $deadline) {
            $result = $this->pollAccessToken($deviceCode);

            if ($result->status === DeviceFlowPollStatus::Success) {
                $this->applyTokenResponse(
                    $result->accessToken ?? '',
                    $result->refreshToken,
                    $result->expiresIn,
                );

                return;
            }

            if ($result->status === DeviceFlowPollStatus::SlowDown) {
                $interval += self::SLOW_DOWN_EXTRA_SECONDS;
            }

            if ($result->status === DeviceFlowPollStatus::ExpiredToken) {
                throw new DiadocApiException('Device Flow: device_code истёк, начните авторизацию заново', 0);
            }

            if ($result->status === DeviceFlowPollStatus::AccessDenied) {
                throw new DiadocApiException('Device Flow: пользователь отклонил авторизацию', 0);
            }

            if ($onPoll !== null) {
                $onPoll($interval);
            }

            \sleep($interval);
        }

        throw new DiadocApiException('Device Flow: время ожидания авторизации истекло', 0);
    }

    /**
     * Обновление access_token по refresh_token.
     *
     * @throws DiadocApiException
     */
    public function refreshAccessToken(): void
    {
        if ($this->refreshToken === null || $this->refreshToken === '') {
            throw new DiadocApiException(
                'refresh_token отсутствует: выполните авторизацию через Device Flow (commands:diadoc-auth-device)',
                0
            );
        }

        $body = $this->buildFormBody(
            [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
            ]
        );

        $data = $this->requestIdentity($this->identityUri(self::IDENTITY_PATH_TOKEN), $body);

        if (empty($data['access_token']) || ! \is_string($data['access_token'])) {
            throw new DiadocApiException('Identity /connect/token: в ответе нет access_token', 0);
        }

        $expiresIn = isset($data['expires_in']) && (int) $data['expires_in'] > 0 ? (int) $data['expires_in'] : null;
        $this->applyTokenResponse($data['access_token'], $this->refreshToken, $expiresIn);
    }

    /**
     * @return array<int, string>
     */
    public function buildRequestHeaders(?string $contentType, string $method, bool $forAuthenticateCall): array
    {
        $token = $this->getToken();
        if ($token === null || $token === '') {
            throw new \Exception('Нет access_token: выполните авторизацию через Device Flow (commands:diadoc-auth-device)');
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
            throw new \Exception('Unauthorized request: нет access_token (OIDC Device Flow)');
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
        return [
            'access_token' => $this->getToken() ?? '',
            'refresh_token' => $this->refreshToken,
            'expires_at' => $this->accessTokenExpiresAt,
        ];
    }

    /**
     * Колбэк вызывается после получения/обновления токенов, чтобы сохранить сессию (кеш, БД).
     *
     * @param callable|null $callback function (array $session): void
     */
    public function setOAuthSessionPersistenceCallback(?callable $callback): void
    {
        $this->oauthSessionPersistenceCallback = $callback;
    }

    public function getOidcScope(): string
    {
        return $this->oidcScope;
    }

    /**
     * Scope по окружению: Diadoc.PublicAPI (прод) / Diadoc.PublicAPI.Staging (стенд).
     */
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

    private function applyTokenResponse(string $accessToken, ?string $refreshToken, ?int $expiresIn): void
    {
        $access = $this->sanitizeForHttpHeader($this->normalizeToken($accessToken));
        if ($access === '') {
            throw new DiadocApiException('Пустой access_token в ответе identity-сервера', 0);
        }

        $this->token = $access;
        $this->refreshToken = $refreshToken !== null && $refreshToken !== '' ? $this->sanitizeForHttpHeader($refreshToken) : null;
        $this->accessTokenExpiresAt = $expiresIn !== null ? \time() + $expiresIn : null;
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
    private function ensureAccessTokenFresh(): void
    {
        match (true) {
            $this->refreshToken === null,
            $this->refreshToken === '',
            $this->accessTokenExpiresAt === null,
            \time() + self::REFRESH_LEEWAY_SECONDS < $this->accessTokenExpiresAt => null,
            default => $this->refreshAccessToken(),
        };
    }

    /**
     * @param array<string, string> $params
     */
    private function buildFormBody(array $params): string
    {
        $encType = \defined('PHP_QUERY_RFC3986') ? PHP_QUERY_RFC3986 : PHP_QUERY_RFC1738;

        return \http_build_query($params, '', '&', $encType);
    }

    /**
     * Полный URI identity-сервера без задвоения слэша (DIADOC_IDENTITY_URL может заканчиваться на '/').
     */
    private function identityUri(string $path): string
    {
        return \rtrim($this->identityBaseUrl, '/') . $path;
    }

    /**
     * Выполнить POST на identity-сервер.
     *
     * @param bool $allowErrorBody разрешить JSON-тело с error (pending/slow_down и т.п.) вместо исключения
     *
     * @return array<array-key, mixed>
     *
     * @throws DiadocApiException
     */
    protected function requestIdentity(string $uri, string $body, bool $allowErrorBody = false): array
    {
        $ch = \curl_init($uri);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 64);
        if (\defined('CURL_HTTP_VERSION_1_1')) {
            \curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }

        \curl_setopt($ch, CURLOPT_POST, 1);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        \curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ]
        );

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
            throw new DiadocApiException('Identity response is not JSON (' . $uri . '): ' . \substr((string) $response, 0, 500), $httpCode);
        }

        $hasError = isset($data['error']) && \is_string($data['error']) && $data['error'] !== '';
        if ($httpCode !== 200 && (! $allowErrorBody || ! $hasError)) {
            throw new DiadocApiException($this->buildIdentityErrorMessage($uri, $data), $httpCode);
        }

        return $data;
    }

    /**
     * Человекочитаемое сообщение об ошибке identity-сервера.
     *
     * Скрывать error_description нельзя: например, KonturId отвечает на неверный scope
     * {"error":"unauthorized_client","error_description":"Invalid scope"}, и без
     * description причина выглядит как «клиент не авторизован для device-флоу», хотя
     * на самом деле clientMode=Device уже включён, а не разрешён нужный scope.
     *
     * В сообщение добавляются client_id и запрошенный scope: это две сущности, которые
     * чаще всего не совпадают с настройками приложения в Кабинете интегратора.
     *
     * @param array<array-key, mixed> $data
     */
    protected function buildIdentityErrorMessage(string $uri, array $data): string
    {
        $error = isset($data['error']) && \is_string($data['error']) && $data['error'] !== '' ? $data['error'] : null;
        $description = isset($data['error_description']) && \is_string($data['error_description']) && $data['error_description'] !== '' ? $data['error_description'] : null;

        $detail = match (true) {
            $error !== null && $description !== null => \sprintf('%s (%s)', $error, $description),
            $error !== null => $error,
            $description !== null => $description,
            default => 'token error',
        };

        return \sprintf(
            'Identity %s: %s; client_id=%s, scope=%s',
            $uri,
            $detail,
            $this->clientId,
            $this->oidcScope,
        );
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function shortDump(array $data): string
    {
        return \substr((string) \json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 0, 500);
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
