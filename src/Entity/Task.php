<?php

namespace App\Entity;

use App\Model\GitHubPullRequest;
use App\Repository\TaskRepository;
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

        #[ORM\Column(type: 'pull_request_type', nullable: true)]
        private ?GitHubPullRequest $pullRequest = null
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
        if (null === $this->pullRequest) {
            return $this->name;
        }

        return str_replace($this->pullRequest->uri, sprintf(
            '[(%s %s) %s](%s)', $this->pullRequest->repo, $this->pullRequest->number, $this->pullRequest->title, $this->pullRequest->uri
        ), $this->name);
    }

    public function setPullRequest(GitHubPullRequest $pullRequest): self
    {
        $this->pullRequest = $pullRequest;

        return $this;
    }
}
