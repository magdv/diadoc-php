Клиент API для diadoc.ru
---------------------------

Клиент API diadoc.ru.

За основу был взят другой клиент.
https://github.com/agentsib/diadoc-php

Работа над ним давно тормознулась и пришлось добавлять новые методы, чтобы оно заработало.

Документация
https://developer.kontur.ru/Docs/diadoc-api/http/PostMessage.html

## Пример

```php
<?php

declare(strict_types=1);

use Diadoc\Proto\GetOrganizationsByInnListRequest;

require __DIR__ . '/vendor/autoload.php';
$api = new \MagDv\Diadoc\DiadocApi(
    '111111111111111111111111111111111',
    'https://diadoc-api.kontur.ru/'
);

$token = $api->authenticateLogin('ddddddddddd@google.com', 'vvllvlvlv');

// это место использовать только если уже есть токен, когда не надо повторно логиниться
$api->setToken($token);


// выводим список контрагентов нашей организации
$orgId = 'ламлвоалоывлолыовлаоыловалоыва';
$contragents = $api->getCountragentsV2($orgId);

// количество контрагентов
var_dump($contragents->getTotalCount());

/** @var Diadoc\Proto\Counteragent $item */
foreach ($contragents->getCounteragents() as $item) {
    $org = $item->getOrganization();
    // пример вывода данных из ответа
    if ($org) {
        $d = [];
        $d['konturId'] = $org->getOrgId();
        $d['inn'] = $org->getInn();
        $d['fullName'] = $org->getFullName();
        $d['shortName'] = $org->getShortName();
        $d['kpp'] = $org->getKpp();
        $d['ogrn'] = $org->getOgrn();
        $d['isRoaming'] = $org->getIsRoaming();
    }
    var_dump($d);
}
```


## Авторизация

### Устаревшая (DiadocAuth) — по умолчанию

Конструктор `DiadocApi` по умолчанию использует устаревший способ авторизации `DiadocAuth` (`ddauth_api_client_id` + `ddauth_token`):

```php
$api = new \MagDv\Diadoc\DiadocApi(
    '111111111111111111111111111111111',
    'https://diadoc-api.kontur.ru/'
);
$token = $api->authenticateLogin('login@example.com', 'password');
$api->setToken($token);
```

### OpenID Connect (OIDC)

Для OIDC-авторизации (Authorization Code + Bearer) используйте статический фабричный метод `DiadocApi::create()`, передав ему стратегию `OidcAuthMode`:

```php
$serviceUrl = 'https://diadoc-api.kontur.ru/';

$api = \MagDv\Diadoc\DiadocApi::create(
    new \MagDv\Diadoc\Auth\OidcAuthMode(
        'oauth-client-id',
        'oauth-client-secret',
        'https://identity.kontur.ru',
        $serviceUrl
    ),
    $serviceUrl
);

// 1. Получить URL авторизации и перенаправить пользователя
$authUrl = $api->buildAuthorizationUrl('https://app.example/oauth/callback', $state);

// 2. После редиректа обменять authorization code на токены
$api->exchangeAuthorizationCode($_GET['code'], 'https://app.example/oauth/callback');

// 3. Токены можно сохранить и восстановить позже
$session = $api->getOAuthSessionState();
$api->setOAuthSession($session['access_token'], $session['refresh_token'], $session['expires_at']);
```

`create()` принимает любую реализацию `AuthModeInterface` — так можно подключить собственный способ авторизации без изменения `DiadocApi`. Для OIDC-стратегии фабрика проверяет, что `serviceUrl` стратегии совпадает с URL клиента, и бросает `\RuntimeException` при рассинхронизации конфигурации.

OIDC автоматически:
- проактивно обновляет `access_token` по `refresh_token` до истечения срока;
- повторяет запрос один раз при `401` с refresh-токеном;
- определяет scope (`Diadoc.PublicAPI` / `Diadoc.PublicAPI.Staging`) по окружению.

Для локального теста OAuth-потока есть встроенный сервер:

```bash
docker compose --profile oauth up oauth-test
# браузер: http://127.0.0.1:8765/
```

## Тесты

     Тесты не дают полной картины работоспособности апи. 
     Мы не можем быть уверены, что нам всегда возвращают нужные данные, т.к. стенд тестовый.
     Тут я скорее проверяют, что обращаюсь куда надо и что плюс-минус все работает.
     
     Для запуска тестов требуется установленное расширение `ext-curl`.

### Протестированные методы (гарантия есть только на них)

- **`MagDv\Diadoc\DiadocApi::authenticateLogin()`** (`tests/Unit/AuthTest.php`)
- **`MagDv\Diadoc\DiadocApi::authenticateLoginV3()`** (`tests/Unit/AuthTest.php`)
- **`MagDv\Diadoc\DiadocApi::getMyOrganizations()`** (`tests/Unit/GetMyOrganizationsTest.php`)
- **`MagDv\Diadoc\DiadocApi::getCountragentsV2()`** (`tests/Unit/CouteragentsTest.php`)
- **`MagDv\Diadoc\DiadocApi::getDocumentTypes()`** (`tests/Unit/DocumentTypesTest.php`)
- **`MagDv\Diadoc\DiadocApi::postMessage()`** (`tests/Unit/MessageTest.php`)
- **`MagDv\Diadoc\DiadocApi::generateSignedContentFromFile()`** (`tests/Unit/SignTest.php`)
- **`MagDv\Diadoc\DiadocApi::shelfUpload()`** (`tests/Unit/ShelfTest.php`, тест помечен как skipped)

## Как вести разработку

В композере я подключил скрипты:
- Для кодстайла `composer fix-style`
- Генерация php классов из proto файлов `composer generate-proto`. Чтобы генерация работала, надо чтобы в системе был установлен `protobuf`
- Запуск Ректора `composer rector` (подключил для разовой помощи, но решил оставить)

Можно также использовать `Makefile` для всех перечисленных выше возможностей.

## Генерация php классов из proto файлов

Вся логика по выборке прото файлов находится в файле `testAuth.php`. 
Если что - то новое появилось в описании апи диадока или вдруг тупо не хватает, то надо это изменить сначала в прото файлах.
- Идем в каталог `proto` тут ищем необходимое или добавляем новое.
- Запукаем `composer generate-proto`
- Смотрим, что у нас сгенерировалось в папке `phpProto`
- Теперь надо заиспользовать новые поля в нашем коде.

Можно также использовать `Makefile` для всех перечисленных выше возможностей.

## Генерация тестового сертификата
https://losst.pro/sozdanie-sertifikata-openssl