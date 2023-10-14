<?php

namespace MagDv\Diadoc\Signer\Interfaces;

use MagDv\Diadoc\Exception\SignerProviderException;

interface SignerProviderInterface
{
    /**
     * Encrypt plain data
     *
     * @param string $plainData Input data
     *
     * @throws SignerProviderException
     *
     * @return string encrypted data in DER format
     */
    public function encrypt(string $plainData): string;

    /**
     * Decrypt encrypted data
     *
     * @param string $encryptedData encrypted data in DER format
     *
     * @throws SignerProviderException
     *
     * @return string encrypted value
     */
    public function decrypt(string $encryptedData): string;

    /**
     * Sign data
     *
     * @param string $data Input data
     *
     * @throws SignerProviderException
     *
     * @return string Signature
     */
    public function sign(string $data): string;

    /**
     * Check signature for input data
     *
     * @param string $data Input data
     * @param string $sign Signature in DER format
     *
     * @throws SignerProviderException
     *
     * @return boolean sign is valid
     */
    public function checkSign(string $data, string $sign): bool;
}
