<?php

declare(strict_types=1);

namespace Test\Unit;

use MagDv\Diadoc\DiadocApi;
use Test\base\BaseTest;
use Test\enums\ConfigNames;

class AuthTest extends BaseTest
{
    public function testAuthenticate(): void
    {
        $api = new DiadocApi(
            getenv(ConfigNames::DD_AUTH),
            getenv(ConfigNames::DIADOC_URL)
        );

        $token = $api->authenticateLogin(getenv(ConfigNames::AUTH_LOGIN), getenv(ConfigNames::AUTH_PASSWORD));

        self::assertNotEmpty($token);
        self::assertTrue(strlen($token) > 50);
    }
}
