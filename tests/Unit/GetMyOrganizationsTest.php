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
}
