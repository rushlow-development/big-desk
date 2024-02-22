<?php

namespace App\Tests\Functional\Controller;

use App\Entity\TodoList;
use App\Factory\TaskFactory;
use App\Factory\TodoListFactory;
use App\Repository\TodoListRepository;
use App\Tests\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TodoListControllerTest extends FunctionalTestCase
{
    private KernelBrowser $client;
    private TodoListRepository $repository;
    private string $path = '/todo';

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);
        $this->repository = static::getContainer()->get(TodoListRepository::class);
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));

        self::assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $fixture = TodoListFactory::createOne([
            'name' => 'My Daily Tasks',
        ]);

        $task = TaskFactory::createOne(['name' => 'old todo', 'todoList' => $fixture]);

        $fixture->addTask($task->object());

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'todo_list[name]' => 'Weekly Tasks',
            'todo_list[tasks][0][name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/');

        /** @var TodoList $fixture */
        $fixture = $this->repository->findAll()[0];

        self::assertCount(1, $fixture->getTasks());
        self::assertSame('Something New', $fixture->getTasks()[0]->getName()); // @phpstan-ignore-line
        self::assertSame('Weekly Tasks', $fixture->getName());
    }

    public function testRemove(): void
    {
        $fixture = TodoListFactory::createOne();

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/');
        self::assertCount(0, $this->repository->findAll());
    }

    public function testRemoveTaskFromAjaxRequest(): void
    {
        $fixture = TodoListFactory::new()->create();

        $taskFixtures = TaskFactory::createMany(2, ['todoList' => $fixture]);
        $taskFixtures[] = $toBeRemoved = TaskFactory::createOne(['todoList' => $fixture]);

        foreach ($taskFixtures as $taskFixture) {
            $fixture->addTask($taskFixture->object());
        }

        $this->client->request('POST', sprintf('%s/%s/task/remove/%s', $this->path, $fixture->getId(), $toBeRemoved->getId()));

        self::assertCount(2, $fixture->getTasks());
    }
}
