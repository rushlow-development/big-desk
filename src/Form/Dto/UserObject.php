<?php

namespace App\Form\Dto;

class UserObject
{
    public function __construct(
        public ?string $displayName = null,

        #[\SensitiveParameter]
        public ?string $username = null,

        #[\SensitiveParameter]
        public ?string $plainPassword = null,
    ) {
    }
}
