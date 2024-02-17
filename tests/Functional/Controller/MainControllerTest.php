<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\NoteFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MainControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndex(): void
    {
        $client = static::createClient();

        NoteFactory::createMany(3);

        $client->request('GET', '/');

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
