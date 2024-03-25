<?php

namespace App\Form\Dto;

class NoteObject
{
    public function __construct(
        public ?string $title = null,
        public ?string $content = null,
    ) {
    }
}
