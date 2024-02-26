<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

        /** @var Collection<int, Task> */
        #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'todoList', cascade: ['persist', 'remove'], orphanRemoval: true)]
        private Collection $tasks = new ArrayCollection(),
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

    /** @return Collection<int, Task> */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        $this->tasks->removeElement($task);

        return $this;
    }
}
