<?php

namespace App\Entity;

use App\Model\GitHubIssue;
use App\Model\GitHubPullRequest;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task extends AbstractEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\ManyToOne(inversedBy: 'tasks')]
        #[ORM\JoinColumn(nullable: false)]
        private TodoList $todoList,

        /** @var Collection<int, GitHubIssue|GitHubPullRequest> */
        #[ORM\Column(type: 'serialized')]
        private Collection $github = new ArrayCollection(),
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTodoList(): TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(TodoList $todoList): static
    {
        $this->todoList = $todoList;

        return $this;
    }

    public function getFormattedName(): string
    {
        $message = $this->name;

        foreach ($this->github as $gitHubObject) {
            $replacement = sprintf('[(%s %s) %s](%s)', $gitHubObject->repo, $gitHubObject->number, $gitHubObject->title, $gitHubObject->uri);
            $message = str_replace($gitHubObject->uri, $replacement, $message);
        }

        return $message;
    }

    /** @return Collection<int, GitHubIssue|GitHubPullRequest> */
    public function getGitHubObjects(): Collection
    {
        return $this->github;
    }

    public function addGitHub(GitHubPullRequest|GitHubIssue $object): self
    {
        $this->github->add($object);

        return $this;
    }

    public function removeGitHub(GitHubPullRequest|GitHubIssue $object): self
    {
        $this->github->removeElement($object);

        return $this;
    }
}
