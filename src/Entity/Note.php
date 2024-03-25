<?php

namespace App\Entity;

use App\Form\Dto\NoteObject;
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

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User $owner,

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

    public function getOwner(): User
    {
        return $this->owner;
    }

    public static function createFromDto(NoteObject $object, User $owner): self
    {
        if (null === $object->title || null === $object->content) {
            throw new \RuntimeException('NoteObject has "null" values');
        }

        return new self(
            title: $object->title,
            owner: $owner,
            content: $object->content
        );
    }

    public function refreshFromDto(NoteObject $object): self
    {
        if (null !== $object->title) {
            $this->title = $object->title;
        }

        $this->content = $object->content;

        return $this;
    }
}
