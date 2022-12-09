<?php

declare(strict_types=1);

namespace Test\Unit;

use Test\base\BaseTest;
use Test\enums\ConfigNames;

class DocumentTypesTest extends BaseTest
{
    public function testGetDocumentTypes(): void
    {
        $api = $this->auth();
        $response = $api->getApi()->getDocumentTypes(getenv(ConfigNames::FROM_BOX_ID));

        $list = [];
        /** @var \Diadoc\Proto\Documents\Types\DocumentTypeDescriptionV2 $type */
        foreach ($response->getDocumentTypes() as $type) {
            $list[] = $type->getName();
        }

        self::assertNotEmpty($list);
    }
}
