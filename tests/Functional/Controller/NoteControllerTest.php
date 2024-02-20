<?php

namespace App\Tests\Functional\Controller;

use App\Factory\NoteFactory;
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
        $this->client->catchExceptions(false);
        $this->repository = static::getContainer()->get(NoteRepository::class);
    }

    public function testNew(): void
    {
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
        $fixture = NoteFactory::createOne(['title' => 'Something old', 'content' => 'something long']);

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));

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
        $fixture = NoteFactory::createOne();

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/');
        self::assertCount(0, $this->repository->findAll());
    }
}
