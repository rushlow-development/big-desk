<?php

namespace App\Tests\Unit\Service;

use App\Model\EncryptedData;
use App\Service\EncryptorService;
use PHPUnit\Framework\TestCase;

class EncryptorServiceTest extends TestCase
{
    public function testEncryption(): void
    {
        $encryptionKey = sodium_bin2hex(sodium_crypto_aead_xchacha20poly1305_ietf_keygen());
        $appSecret = '1234';
        $message = 'Here is my message';

        $encryptor = new EncryptorService($encryptionKey, $appSecret);

        $result = $encryptor->encryptData($message);

        self::assertNotSame($message, $result->data);

        $decryptedData = $encryptor->decryptData($result);

        self::assertSame($message, $decryptedData);

        $withBadKey = new EncryptorService(sodium_bin2hex(sodium_crypto_aead_xchacha20poly1305_ietf_keygen()), $appSecret);
        self::assertNull($withBadKey->decryptData($result));

        $withBadSecret = new EncryptorService($encryptionKey, 'badSecret');
        self::assertNull($withBadSecret->decryptData($result));

        $badNonce = sodium_bin2base64(
            random_bytes(\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES),
            \SODIUM_BASE64_VARIANT_ORIGINAL
        );

        $withBadNonce = new EncryptedData($result->data, $badNonce);

        self::assertNull($encryptor->decryptData($withBadNonce));
    }
}
