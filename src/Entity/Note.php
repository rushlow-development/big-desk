<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note extends AbstractEntity
{
    public function __construct(
        #[Assert\NotBlank, Assert\Length(max: 255)]
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $content = null,
    ) {
        parent::__construct();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
