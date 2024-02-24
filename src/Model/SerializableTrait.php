<?php

namespace App\Model;

trait SerializableTrait
{
    //    #[\Override]
    //    public function serialize(): string
    //    {
    //        return json_encode(['sName' => self::class, 'sObject' => $this->__serialize()], JSON_THROW_ON_ERROR);
    //    }
    //
    //    #[\Override]
    //    public function unserialize(string $data): self
    //    {
    //        $data = json_decode(json: $data, associative: true, flags: \JSON_THROW_ON_ERROR);
    //
    //        $this->__unserialize($data['sObject']);
    //
    //        return $this;
    //    }

    public function __serialize(): array
    {
        $data = [];

        foreach ((new \ReflectionClass($this))->getProperties() as $rProperty) {
            $data[$rProperty->getName()] = $rProperty->getValue($this);
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $propertyName => $value) {
            $this->$propertyName = $value;
        }
    }
}
