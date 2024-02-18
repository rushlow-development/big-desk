<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\NoteFactory;
use App\Tests\FunctionalTestCase;

class MainControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        NoteFactory::createMany(3);

        $client->request('GET', '/');

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
