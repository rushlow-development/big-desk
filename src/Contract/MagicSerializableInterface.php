<?php

namespace App\Contract;

interface MagicSerializableInterface
{
    public function __serialize(): array;

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void;
}
