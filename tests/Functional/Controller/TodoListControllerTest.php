<?php

namespace App\Tests\Functional\Controller;

use App\Entity\TodoList;
use App\Factory\TaskFactory;
use App\Factory\TodoListFactory;
use App\Factory\UserFactory;
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
        $this->repository = static::getContainer()->get(TodoListRepository::class);
    }

    public function testNew(): void
    {
        $user = UserFactory::createOne();
        $path = sprintf('%s/new', $this->path);

        // Deny - Not Authenticated
        $this->client->request('GET', $path);
        self::assertResponseRedirects('/login');

        // Allow authenticated users to create lists
        $this->client->loginUser($user->object());
        $this->client->request('GET', $path);

        self::assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $user = UserFactory::createOne();
        $fixture = TodoListFactory::createOne([
            'name' => 'My Daily Tasks',
            'owner' => $user,
        ]);
        $task = TaskFactory::createOne(['name' => 'old todo', 'todoList' => $fixture]);
        $fixture->addTask($task->object());
        $path = sprintf('%s/%s/edit', $this->path, $fixture->getId());

        // Deny - Not Authenticated
        $this->client->request('GET', $path);
        self::assertResponseRedirects('/login');

        // Deny - Not Owner
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('GET', $path);
        self::assertResponseStatusCodeSame(403);

        // Allow owner to edit list
        $this->client->loginUser($user->object());
        $this->client->request('GET', $path);
        self::assertResponseIsSuccessful();

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
        $user = UserFactory::createOne();
        $fixture = TodoListFactory::createOne(['owner' => $user]);
        $path = sprintf('%s/%s/delete', $this->path, $fixture->getId());

        // Deny - Not Authenticated
        $this->client->request('POST', $path);
        self::assertResponseRedirects('/login');

        // Deny - Not Owner
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('POST', $path);
        self::assertResponseStatusCodeSame(403);

        // Ensure valid CSRF token is required to delete
        $this->client->loginUser($user->object());
        $this->client->request('POST', $path);
        self::assertCount(1, $this->repository->findAll());

        // Get a valid CSRF token from the template
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $csrfToken = $crawler->filter('input[name=_token]')->first()->attr('value');
        self::assertNotEmpty($csrfToken);

        // Allow owner to delete with valid CSRF token
        $this->client->request('POST', $path, ['_token' => $csrfToken]);

        self::assertResponseRedirects('/');
        self::assertCount(0, $this->repository->findAll());

        // Make sure the "delete" button on the edit page functions
        $fixture = TodoListFactory::createOne(['owner' => $user]);
        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/');
        self::assertCount(0, $this->repository->findAll());
    }

    public function testRemoveTaskFromAjaxRequest(): void
    {
        $user = UserFactory::createOne();
        $fixture = TodoListFactory::createOne(['owner' => $user]);

        $taskFixtures = TaskFactory::createMany(2, ['todoList' => $fixture]);
        $taskFixtures[] = $toBeRemoved = TaskFactory::createOne(['todoList' => $fixture]);

        foreach ($taskFixtures as $taskFixture) {
            $fixture->addTask($taskFixture->object());
        }

        $path = sprintf('%s/%s/task/remove/%s', $this->path, $fixture->getId(), $toBeRemoved->getId());

        // Deny - Not Authenticated
        $this->client->request('POST', $path);
        self::assertResponseRedirects('/login');

        // Deny - User does not own the list
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('POST', $path);
        $this->assertResponseStatusCodeSame(403);

        // Allow owner to remove a task
        $this->client->loginUser($user->object());
        $this->client->request('POST', $path);

        self::assertCount(2, $fixture->getTasks());
    }
}
