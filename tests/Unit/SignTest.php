<?php

declare(strict_types=1);

namespace Test\Unit;

use Diadoc\Proto\DocumentType;
use Diadoc\Proto\Events\AttachmentType;
use Diadoc\Proto\Events\DocumentAttachment;
use Diadoc\Proto\Events\MessageToPost;
use Diadoc\Proto\Events\SignedContent;
use MagDv\Diadoc\Exception\SignerProviderException;
use Test\base\BaseTest;
use Test\enums\ConfigNames;

class SignTest extends BaseTest
{
    public function testSign(): void
    {
        $api = $this->auth();
        $file = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'ECN.xml';

        try {
            $api->getApi()->generateSignedContentFromFile($file);
        } catch (\Throwable $exception) {
            // Мы ловим эксепшен, т.к. в сборке у меня не настроены все бибилиотеки Openssl
            self::assertInstanceOf(SignerProviderException::class, $exception);
            self::assertEquals(
                'Ошибка при подписании файла ERR > smime: Unknown option or cipher: gost89
smime: Use -help for summary.
',
                $exception->getMessage()
            );
        }
    }
}
