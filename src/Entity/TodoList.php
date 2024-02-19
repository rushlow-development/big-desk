<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 */
#[ORM\Entity(repositoryClass: TodoListRepository::class)]
class TodoList extends AbstractEntity
{
    public function __construct(
        #[ORM\Column]
        private string $name,

        /** @var string[] */
        #[ORM\Column]
        private array $tasks = [],
    ) {
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return string[] */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function addTask(string $task): self
    {
        $this->tasks[] = $task;

        return $this;
    }

    public function removeTask(string $task): self
    {
        foreach ($this->tasks as $key => $value) {
            if ($task === $value) {
                unset($this->tasks[$key]);
            }
        }

        return $this;
    }
}
