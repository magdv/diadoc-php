<?php

declare(strict_types=1);

namespace Test\Unit;

use Diadoc\Proto\DocumentType;
use Diadoc\Proto\Events\DocumentAttachment;
use Diadoc\Proto\Events\Message;
use Diadoc\Proto\Events\MessageToPost;
use Diadoc\Proto\Events\SignedContent;
use MagDv\Diadoc\BoxApi;
use MagDv\Diadoc\DiadocApi;
use Test\base\BaseTest;
use Test\enums\ConfigNames;

class GetEntityContentTest extends BaseTest
{
    public function testGetEntityContent(): void
    {
        $api = $this->auth()->getApi();
        $message = $this->postMessageWithDocument($api);

        $entityId = null;
        foreach ($message->getEntities() as $entity) {
            $entityId = $entity->getEntityId();
            break;
        }
        self::assertNotEmpty($entityId, 'У отправленного сообщения нет сущностей.');

        $content = $api->getEntityContent($message->getFromBoxId(), $message->getMessageId(), $entityId);
        self::assertNotEmpty($content);
        self::assertStringContainsString('<?xml', $content);

        $boxApi = new BoxApi($api, $message->getFromBoxId());
        self::assertEquals($content, $boxApi->getEntityContent($message->getMessageId(), $entityId));
    }

    private function postMessageWithDocument(DiadocApi $api): Message
    {
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

        return $api->postMessage($postObject);
    }
}
