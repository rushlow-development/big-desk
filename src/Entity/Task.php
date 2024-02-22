<?php

namespace App\Entity;

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
}
