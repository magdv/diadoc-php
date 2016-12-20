<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
//$api = new \MagDv\Diadoc\DiadocApi(
//    'kdv-a9081f6e-626c-4fd3-8e6c-8ff0ed7de0b2',
//    'https://diadoc-api-staging.kontur.ru/'
//);
//
////$resp = $api->authenticateLogin('d.narezhneva@magdv.com', 'kdvkonturkdvkontur');
////var_dump($resp);
//
//$api->setToken('w0peBgrzUEh57f2kkVZyzirSrP8WrE2P8nZ5LbvyrtNe4BvTbSzmHTPfPGGJUHGx4mubkL8HQpDKzzYdLLwZb9qegZdjaUQnMegrSHGDb6lnv7hJSogr4AtGu89zdoR78SoABEqdJNT3xIC+hO8WnisX1p/tdfNsFgIrgnlKidk=');
//
////$orgId = '360d51ee-66dd-4f9a-ba62-ac49f0b239ed';
//$orgId = 'e86a764e-a447-4b18-a2b1-a88c7d637835';
//$contragents = $api->getCountragentsV2($orgId, null, null);
//
//var_dump($contragents->getTotalCount());
////var_dump($contragents->getCounteragents());
///** @var Diadoc\Proto\Counteragent $item */
//foreach ($contragents->getCounteragents() as $item) {
//    $org = $item->getOrganization();
//    if ($org) {
//        $d = [];
//        $d['konturId'] = $org->getOrgId();
//        $d['inn'] = $org->getInn();
//        $d['fullName'] = $org->getFullName();
//        $d['shortName'] = $org->getShortName();
//        $d['kpp'] = $org->getKpp();
//        $d['ogrn'] = $org->getOgrn();
//        $d['isRoaming'] = $org->getIsRoaming();
//    }
//    var_dump($d);
//}


//$request = new GetOrganizationsByInnListRequest();
//$request->setInnList(['ddddd', 'qweqweqweqweqw']);
//var_dump($request->serializeToJsonString());
//var_dump(json_decode($request->serializeToJsonString(), true));
//var_dump(http_build_query(json_decode($request->serializeToJsonString(), true)));