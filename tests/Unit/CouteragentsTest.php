<?php

declare(strict_types=1);

namespace Test\Unit;

use Test\base\BaseTest;
use Test\enums\ConfigNames;

class CouteragentsTest extends BaseTest
{
    public function testGetCounteragents(): void
    {
        $api = $this->auth();

        $contragents = $api->getApi()->getCountragentsV2(getenv(ConfigNames::ORG_ID));
        self::assertNotEmpty($contragents);

        $d = [];
        $count = $contragents->getTotalCount();

        self::assertTrue($count > 0);
        foreach ($contragents->getCounteragents() as $item) {
            /** @var \Diadoc\Proto\Organization $org */
            $org = $item->getOrganization();
            if ($org) {
                $d = [];
                $d['konturId'] = $org->getOrgId();
                $d['inn'] = $org->getInn();
                $d['fullName'] = $org->getFullName();
                $d['shortName'] = $org->getShortName();
                $d['kpp'] = $org->getKpp();
                $d['ogrn'] = $org->getOgrn();
                $d['isRoaming'] = $org->getIsRoaming();
                $d['isForeign'] = $org->getIsForeign();
            }
        }

        self::assertTrue(count($d) > 0);
    }
}
