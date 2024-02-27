<?php

declare(strict_types=1);

namespace Test\Unit;

use Diadoc\Proto\DocumentType;
use Diadoc\Proto\Events\AttachmentType;
use Diadoc\Proto\Events\DocumentAttachment;
use Diadoc\Proto\Events\MessageToPost;
use Diadoc\Proto\Events\SignedContent;
use Test\base\BaseTest;
use Test\enums\ConfigNames;

class MessageTest extends BaseTest
{
    public function testPostMessage(): void
    {
        $api = $this->auth();
        $postObject = new MessageToPost();
        $postObject->setFromBoxId(getenv(ConfigNames::FROM_BOX_ID));
        $postObject->setToBoxId(getenv(ConfigNames::TO_BOX_ID));

        $xml = file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'ECN.xml');

        $content = new SignedContent();
        $content->setContent($xml);

        $attachment = new DocumentAttachment();
        $attachment->setSignedContent($content);
        $attachment->setTypeNamedId(DocumentType::name(DocumentType::LogisticsWaybill));

        $postObject->setDocumentAttachments([$attachment]);

        /** @var \Diadoc\Proto\Events\Message $response */
        $response = $api->getApi()->postMessage($postObject);

        self::assertNotEmpty($response);
        self::assertNotEmpty($response->getMessageId());
        self::assertNotEmpty($response->getFromBoxId());
        self::assertNotEmpty($response->getFromTitle());
        self::assertNotEmpty($response->getToBoxId());
        self::assertNotEmpty($response->getToTitle());

        $entityId = null;
        /** @var \Diadoc\Proto\Events\Entity $item */
        foreach ($response->getEntities() as $item) {
            $entityId = $item->getEntityId();
            self::assertNotEmpty($item->getEntityId());
            self::assertEquals(AttachmentType::Title, $item->getAttachmentType());
            self::assertEquals(
                'ON_TRNACLGROT_2BM-7715290822-332801001-201505310156089197087_2BM-7017094419-2012052808201742382630000000000_2BM-7017477919-701701001-202009220246067913748_0_20220624_f31045c7-a0be-409e-bb89-a0d436053944.xml',
                $item->getFileName()
            );
        }

        self::assertTrue(true);

//        Это было для проверки. Оставлю на будущее.
//        $documentResponse = $api->getApi()->getDocument(
//            'a187f153-8fae-4c3b-b7f6-348e2d018b9d',
//            '8b78b11d-3154-4127-8727-f487328d2ce6',
//            '18e82bb2-b77a-4ff5-b064-f25ff5615b8b',
//            'false'
//        );
//
//        self::assertEquals($documentResponse->getMessageId(), $response->getMessageId());
    }
}
