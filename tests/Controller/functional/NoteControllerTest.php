<?php

namespace App\Test\Controller;

use App\Repository\NoteRepository;
use App\Tests\Factory\NoteFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NoteControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;
    private NoteRepository $repository;
    private string $path = '/note';

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get(NoteRepository::class);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Note index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'note[title]' => 'Testing',
            'note[content]' => 'Testing',
        ]);

        self::assertResponseRedirects(sprintf('/note/%s', $this->repository->findAll()[0]->getId()));

        self::assertCount(1, $this->repository->findAll());
    }

    public function testShow(): void
    {
        $fixture = NoteFactory::createOne();

        $this->client->request('GET', sprintf('%s/%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Note');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixture = NoteFactory::createOne(['title' => 'Something old', 'content' => 'something long']);

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'note[title]' => 'Something New',
            'note[content]' => 'Something Short',
        ]);

        self::assertResponseRedirects(sprintf('%s/%s', $this->path, $fixture->getId()));

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something Short', $fixture[0]->getContent());
    }

    public function testRemove(): void
    {
        $fixture = NoteFactory::createOne();

        $this->client->request('GET', sprintf('%s/%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects($this->path);
        self::assertCount(0, $this->repository->findAll());
    }
}
