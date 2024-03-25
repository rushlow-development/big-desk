<?php

namespace App\Entity;

use App\Doctrine\Type\EncryptedDataType;
use App\Model\EncryptedData;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['username', 'displayName'])]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        #[ORM\Column(length: 180)]
        private string $displayName,

        #[\SensitiveParameter]
        #[ORM\Column(length: 180)]
        private string $username,

        #[\SensitiveParameter]
        #[ORM\Column]
        private string $password,

        /** @var list<string> The user roles */
        #[ORM\Column]
        private array $roles = [],

        #[ORM\Column(type: EncryptedDataType::ENCRYPTED_DATA_TYPE, nullable: true)]
        private ?EncryptedData $gitHubToken = null,

        /** @var Collection<int, Note> */
        #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'owner', orphanRemoval: true)]
        private Collection $notes = new ArrayCollection()
    ) {
        parent::__construct();
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /** @return list<string> */
    #[\Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Override]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getGitHubToken(): ?EncryptedData
    {
        return $this->gitHubToken;
    }

    public function setGitHubToken(?EncryptedData $token): self
    {
        $this->gitHubToken = $token;

        return $this;
    }

    /** @return Collection<int, Note> */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    //    public function addNote(Note $note): static
    //    {
    //        if (!$this->notes->contains($note)) {
    //            $this->notes->add($note);
    //        }
    //
    //        return $this;
    //    }
}
