Клиент API для diadoc.ru
---------------------------

Клиент API diadoc.ru.

За основу был взят другой клиент.
https://github.com/agentsib/diadoc-php

Работа над ним давно тормознулась и пришлось добавлять новые методы, чтобы оно заработало.

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


## Тесты

     Тест не дает полной картины работоспособности апи. 
     Мы не можем быть уверены, что нам всегда возвращают нужные данные, т.к. стенд тестовый.
     Тут я скорее проверяют, что обращаюсь куда надо и что плюс-минус все работает.

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