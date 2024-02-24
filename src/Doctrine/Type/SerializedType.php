<?php

namespace App\Doctrine\Type;

use App\Contract\MagicSerializableInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

class SerializedType extends Type
{
    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (!\is_object($value)) {
            return null;
        }

        if ($value instanceof Collection) {
            $items = [];

            foreach ($value as $collectionItem) {
                $items[$collectionItem::class] = $collectionItem;
            }

            try {
                return json_encode(['objectName' => $value::class, 'data' => $items], \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION);
            } catch (\JsonException $e) {
                throw SerializationFailed::new($value, 'json', $e->getMessage(), $e);
            }
        }

        return $this->serialize($value);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (\is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (!\is_string($value)) {
            return null;
        }

        try {
            $valueArray = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
            if (!\is_array($valueArray)) {
                return null;
            }
        } catch (\JsonException $e) {
            throw ValueNotConvertible::new($value, 'json', $e->getMessage(), $e);
        }

        if (empty($valueArray['objectName'])) {
            throw new \RuntimeException('Data does not conform to SerializedType specification.');
        }

        $r = new \ReflectionClass($valueArray['objectName']);
        $isCollection = $r->implementsInterface(Collection::class);
        $isSerializable = $r->implementsInterface(MagicSerializableInterface::class);

        if ($isCollection) {
            $collection = $r->newInstanceWithoutConstructor();

            foreach ($valueArray['data'] as $itemClass => $collectionItem) {
                $rCollectionItem = new \ReflectionClass($itemClass);

                $collection->add($this->unserialize($rCollectionItem, $collectionItem));
            }

            return $collection;
        }

        if (!$isSerializable) {
            throw new \RuntimeException('Only objects that implement '.MagicSerializableInterface::class.' are allowed.');
        }

        return $this->unserialize($r, $valueArray['data']);
    }

    /**
     * @throws SerializationFailed
     */
    protected function serialize(object $object): string
    {
        if (!$object instanceof MagicSerializableInterface) {
            throw new \RuntimeException('Class must implement '.MagicSerializableInterface::class);
        }

        try {
            return json_encode(['objectName' => $object::class, 'data' => $object->__serialize()], \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION);
        } catch (\JsonException $e) {
            throw SerializationFailed::new($object, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected function unserialize(\ReflectionClass $reflectedObject, array $objectData): object
    {
        $object = $reflectedObject->newInstanceWithoutConstructor();
        $object->__unserialize($objectData);

        return $object;
    }
}
