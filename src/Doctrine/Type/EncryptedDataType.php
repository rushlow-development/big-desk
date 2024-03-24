<?php

namespace App\Doctrine\Type;

use App\Model\EncryptedData;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EncryptedDataType extends Type
{
    public const string ENCRYPTED_DATA_TYPE = 'encrypted_data';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?EncryptedData
    {
        if (!\is_string($value)) {
            return null;
        }

        /** @var array<mixed> $jsonValue */
        $jsonValue = json_decode($value, true, flags: \JSON_THROW_ON_ERROR);

        if ((empty($jsonValue['data'] ?? '') || !\is_string($jsonValue['data'])) || (empty($jsonValue['nonce']) || !\is_string($jsonValue['nonce']))) {
            throw new \RuntimeException('Cannot convert value to EncryptedData object.');
        }

        return new EncryptedData($jsonValue['data'], $jsonValue['nonce']);
    }
}
