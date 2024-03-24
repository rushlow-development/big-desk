<?php

namespace App\Model;

final readonly class EncryptedData implements \Stringable
{
    public function __construct(
        #[\SensitiveParameter]
        public string $data,

        #[\SensitiveParameter]
        public string $nonce,
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return json_encode(['data' => $this->data, 'nonce' => $this->nonce], flags: \JSON_THROW_ON_ERROR);
    }
}
