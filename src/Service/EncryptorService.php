<?php

namespace App\Service;

use App\Model\EncryptedData;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EncryptorService
{
    public function __construct(
        #[\SensitiveParameter]
        #[Autowire('%app.encryption_key%')]
        private readonly string $encryptionSecret,

        #[\SensitiveParameter]
        #[Autowire('%kernel.secret%')]
        private readonly string $appSecret,
    ) {
    }

    public function encryptData(string $data): EncryptedData
    {
        $nonce = random_bytes(\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

        $encryptedData = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            message: $data,
            additional_data: json_encode(value: ['app_secret' => $this->appSecret], flags: \JSON_THROW_ON_ERROR),
            nonce: $nonce,
            key: sodium_hex2bin($this->encryptionSecret)
        );

        return new EncryptedData(
            data: sodium_bin2base64($encryptedData, \SODIUM_BASE64_VARIANT_ORIGINAL),
            nonce: sodium_bin2base64($nonce, \SODIUM_BASE64_VARIANT_ORIGINAL),
        );
    }

    public function decryptData(EncryptedData $data): ?string
    {
        $decryptedData = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            ciphertext: sodium_base642bin($data->data, \SODIUM_BASE64_VARIANT_ORIGINAL),
            additional_data: json_encode(['app_secret' => $this->appSecret], flags: \JSON_THROW_ON_ERROR),
            nonce: sodium_base642bin($data->nonce, \SODIUM_BASE64_VARIANT_ORIGINAL),
            key: sodium_hex2bin($this->encryptionSecret)
        );

        return empty($decryptedData) ? null : $decryptedData;
    }
}
