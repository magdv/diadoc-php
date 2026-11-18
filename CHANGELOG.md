# CHANGELOG

## Unreleased

### Added

- Параметризация таймаутов HTTP-запросов: `MagDv\Diadoc\Entities\Http\HttpTimeouts`
  (timeout — простой соединения/передачи, maxDuration — жёсткий потолок на запрос).
- `DiadocApi::setTimeouts()` / `DiadocApi::getTimeouts()` и необязательный аргумент
  `$timeouts` в конструкторе и `DiadocApi::create()`.
- `HttpTimeoutsAwareInterface` — auth-режимы (`AuthenticateV3AuthMode`,
  `OidcAuthFlowAuthMode`, `OidcDeviceFlowAuthMode`) принимают те же параметры.

### Changed

- Восстановлена совместимость с PHP 8.2: сняты типы с констант
  (`public const string ...` — синтаксис PHP 8.3). Значения и поведение не изменились.

### Backward compatibility

- Поведение по умолчанию не изменилось: если HttpTimeouts не передан, используются
  прежние таймауты (20 с для DiadocApi, 15/30 с для OIDC-режимов, 20 с для authenticate_v3).

## 1.0.0

Включает изменения upstream 0.7.0 (HTTP-логгер) и 0.7.1 (`DiadocApi::getNewEventsV8()`,
`BoxApi::getNewEventsV8()`).

### Added

- Авторизация через OIDC Device Flow (RFC 8628) — режим `OidcDeviceFlowAuthMode`:
  - DTO шагов device-flow: `DeviceFlowStart`, `DeviceFlowPollStatus`, `DeviceFlowPollResult`;
  - первичная авторизация выполняется один раз (CLI-командой потребителя), далее refresh_token
    обновляет access_token автоматически; при 401 — один ретрай с refresh.
- PSR-16-хранилище авторизационной сессии (передаётся необязательным параметром):
  сессия OIDC и ddauth-токен восстанавливаются при старте и сохраняются при обновлении.
- `AuthenticateV3AuthMode` умеет сам получать ddauth-токен по login/password и перелогиниваться при 401.

### Changed

- `OidcAuthMode` переименован в `OidcAuthFlowAuthMode` (Authorization Code Flow).
- `AuthModeInterface::ensureReady()` переименован в `ensureAuthorized()`.
- Код прогнан автофиксерами проекта-потребителя: typed class constants (PHP 8.3+),
  `final`-классы, `readonly`-конструкторы.

## 0.7.0

### Added

- Абстракция логгера HTTP-запросов:
  - `HttpLoggerInterface` — контракт логгера (`log(HttpLogDto): void`);
  - `HttpLogDto` — DTO с данными запроса/ответа (`url`, `method`, `response`, `statusCode`, `params`);
  - `StdoutHttpLogger` — реализация, пишущая лог в STDOUT.
- Интеграция в `DiadocApi`:
  - `setLogger(?HttpLoggerInterface)` — установка логгера (доступен для объектов, созданных и конструктором, и `create()`);
  - логируются и успешные, и ошибочные ответы (в т.ч. 401).

### Backward compatibility

- Конструктор `DiadocApi` и фабрика `DiadocApi::create()` не изменились; логгер подключается опционально через `setLogger()`.

## 0.6.0

### Added

- OpenID Connect авторизация (Authorization Code + Bearer) через `DiadocApi::create()` со стратегией `OidcAuthMode`.
- Стратегии авторизации в `src/Auth/`:
  - `AuthModeInterface` — контракт стратегии;
  - `AuthenticateV3AuthMode` — устаревший DiadocAuth (обратная совместимость);
  - `OidcAuthMode` — OIDC (Bearer + refresh + retry).
- OIDC-методы в `DiadocApi`:
  - `buildAuthorizationUrl()`
  - `exchangeAuthorizationCode()`
  - `refreshAccessToken()`
  - `setOAuthSession()`
  - `getOAuthSessionState()`
  - `setOAuthSessionPersistenceCallback()`
  - `getOidcScope()`
  - `getAuthMode()`
  - `setLegacyToken()`
- Автоматическое управление жизненным циклом токена:
  - проактивный refresh по `expires_at`;
  - один ретрай на `401` с refresh-токеном.
- Определение scope по окружению:
  - `Diadoc.PublicAPI` для production;
  - `Diadoc.PublicAPI.Staging` для staging/test (`diadoc-api-staging`, `diadoc-api-test`).
- Локальный OAuth-тест-сервер:
  - `docker/oauth-test/router.php`;
  - `docker compose --profile oauth up oauth-test`.

### Backward compatibility

- Конструктор `DiadocApi` не изменился: `new DiadocApi($ddauthApiClientId, $serviceUrl, $debug, $signerProvider)` по-прежнему использует устаревший DiadocAuth.
- Для произвольного режима авторизации (в т.ч. OIDC) используйте статический фабричный метод `DiadocApi::create(AuthModeInterface $authMode, $serviceUrl, $debug, $signerProvider)`. Для OIDC передайте `new OidcAuthMode($clientId, $clientSecret, $identityBaseUrl, $serviceUrl)`; фабрика проверяет совпадение `serviceUrl` стратегии и клиента (`\RuntimeException` при рассинхронизации).

### Updated

- `.env.example` — добавлены OIDC-переменные.
- `docker-compose.yml` — добавлен сервис `oauth-test`.
- Тесты: `ApiClient`, `AuthTest`, `ConfigNames`, `SimpleFileCache`.

## 0.0.3

- Generate signed content not only from file
- Fix default timezone in `DateHelper`

## 0.0.2

- Methods for Events
- Wrappers `BoxApi`, `OrganizationApi`
- `GetDocument` method
- Bugfix

## 0.0.1

Init