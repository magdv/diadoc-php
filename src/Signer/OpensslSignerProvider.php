<?php

namespace MagDv\Diadoc\Signer;

use MagDv\Diadoc\Exception\SignerProviderException;
use MagDv\Diadoc\Signer\Interfaces\SignerProviderInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class OpensslSignerProvider implements SignerProviderInterface
{
    public function __construct(
        private string $caFile,
        private string $certFile,
        private string $privateKey,
        private string $opensslBin = '/usr/bin/openssl'
    ) {
    }

    public function encrypt(string $plainData): string
    {
        $process = $this->getOpensslProcess(
            [
                'smime',
                '-encrypt',
                '-binary',
                '-noattr',
                '-outform', 'DER',
                '-gost89',
                $this->certFile
            ],
            $plainData
        );

        try {
            return $process->mustRun()->getOutput();
        } catch (RuntimeException $runtimeException) {
            throw new SignerProviderException($runtimeException->getMessage(), $runtimeException->getCode(), $runtimeException);
        }
    }

    public function decrypt(string $encryptedData): string
    {
        $process = $this->getOpensslProcess(
            [
                'smime',
                '-decrypt',
                '-binary',
                '-noattr',
                '-inform', 'der',
                '-inkey', $this->privateKey
            ],
            $encryptedData
        );

        try {
            return $process->mustRun()->getOutput();
        } catch (RuntimeException $runtimeException) {
            throw new SignerProviderException($runtimeException->getMessage(), $runtimeException->getCode(), $runtimeException);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sign(string $data): string
    {
        $process = $this->getOpensslProcess(
            [
                'smime',
                '-sign',
                '-binary',
                '-noattr',
                '-gost89',
                '-signer', $this->certFile,
                '-inkey', $this->privateKey,
                '-outform', 'der'
            ],
            $data
        );

        try {
            $out = $process->mustRun();

            $process->run(static function ($type, $buffer) {
                if (Process::ERR === $type) {
                    throw new SignerProviderException('Ошибка при подписании файла ERR > '.$buffer, 500);
                }
            });
            return $out->getOutput();
        } catch (\Throwable $throwable) {
            throw new SignerProviderException($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    public function checkSign(string $data, string $sign): bool
    {
        $file = tmpfile();
        $metaDatas = stream_get_meta_data($file);
        $tmpFilename = $metaDatas['uri'];
        fwrite($file, $data);

        $process = $this->getOpensslProcess(
            [
                'smime',
                '-verify',
                '-binary',
                '-noattr',
                '-gost89',
                '-inform', 'der',
                '-CAfile', $this->caFile,
                '-content', $tmpFilename
            ],
            $sign
        );

        try {
            $result = $process->run();
            fclose($file);

            return $result === 0;
        } catch (RuntimeException $runtimeException) {
            throw new SignerProviderException($runtimeException->getMessage(), $runtimeException->getCode(), $runtimeException);
        }
    }

    private function getOpensslProcess(array $args = [], string $input = null): Process
    {
        $arguments = array_merge([$this->opensslBin], $args);
        $process = new Process($arguments);
        $process->setInput($input);
        return $process;
    }
}
