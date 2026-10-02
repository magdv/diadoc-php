<?php

declare(strict_types=1);

namespace Test\Unit;

use Test\base\BaseTest;
use Test\enums\ConfigNames;

class GetMyOrganizationsTest extends BaseTest
{
    public function testGetMyOrganizations(): void
    {
        $api = $this->auth();

        $organizations = $api->getApi()->getMyOrganizations();
        self::assertTrue($organizations->getOrganizations()->count() > 0);
    }
    public function testGetOrganizationsByInnKpp(): void
    {
        $api = $this->auth();
        $inn = '5260168445';
        $kpp = '770301001';

        $organizations = $api->getApi()->getOrganizationsByInnKpp(inn: $inn, kpp: $kpp);
        $boxId = null;
        foreach ($organizations->getOrganizations() as $organization) {
            if ('2BM-5260168445-2012052808352999262630000000000' === $organization->getFnsParticipantId()) {
                foreach ($organization->getBoxes() as $box) {
                    $boxId = $box->getBoxId();
                }
            }
        }
        self::assertNotEmpty($organizations);
        self::assertNotEmpty($boxId);
    }
}
