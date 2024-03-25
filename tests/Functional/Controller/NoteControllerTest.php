<?php

namespace App\Tests\Functional\Controller;

use App\Factory\NoteFactory;
use App\Factory\UserFactory;
use App\Repository\NoteRepository;
use App\Tests\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class NoteControllerTest extends FunctionalTestCase
{
    private KernelBrowser $client;
    private NoteRepository $repository;
    private string $path = '/note';

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get(NoteRepository::class);
    }

    public function testNew(): void
    {
        $user = UserFactory::createOne();
        $uri = sprintf('%s/new', $this->path);

        // Deny - Not Authenticated
        $this->client->request('GET', $uri);
        self::assertResponseRedirects('/login');

        // Allow authenticated user to create a note
        $this->client->loginUser($user->object());
        $this->client->request('GET', sprintf('%s/new', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'note[title]' => 'Testing',
            'note[content]' => 'Testing',
        ]);

        self::assertResponseRedirects('/');
        self::assertCount(1, $this->repository->findAll());
    }

    public function testEdit(): void
    {
        $user = UserFactory::createOne();
        $fixture = NoteFactory::createOne(['title' => 'Something old', 'content' => 'something long', 'owner' => $user]);
        $uri = sprintf('%s/%s/edit', $this->path, $fixture->getId());

        // Deny - Not Authenticated
        $this->client->request('GET', $uri);

        self::assertResponseRedirects('/login');

        // Deny - Not Owner of Note
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('GET', $uri);

        self::assertResponseStatusCodeSame(403);

        // Allows Owner of Note to modify it
        $this->client->loginUser($user->object());
        $this->client->request('GET', $uri);
        $this->client->submitForm('Update', [
            'note[title]' => 'Something New',
            'note[content]' => 'Something Short',
        ]);

        self::assertResponseRedirects('/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something Short', $fixture[0]->getContent());
    }

    public function testRemove(): void
    {
        $user = UserFactory::createOne();
        $fixture = NoteFactory::createOne(['owner' => $user]);
        $uri = sprintf('%s/%s/edit', $this->path, $fixture->getId());

        // Deny - Not Authenticated
        $this->client->request('GET', $uri);

        self::assertResponseRedirects('/login');

        // Deny - Not Owner of Note
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('GET', $uri);

        self::assertResponseStatusCodeSame(403);

        // Allows Owner of Note to delete it
        $this->client->loginUser($user->object());
        $this->client->request('GET', $uri);
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/');
        self::assertCount(0, $this->repository->findAll());
    }
}
