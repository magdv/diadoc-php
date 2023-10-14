<?php

declare(strict_types=1);

namespace Test\Unit;

use Test\base\BaseTest;

class ShelfTest extends BaseTest
{
    public function testSign(): void
    {
        $api = $this->auth();
        $content = 'ssss';
        $response = $api->getApi()->shelfUpload('sd', 1, $content, 0);
        self::assertEquals('[0]', $response);
    }
}
