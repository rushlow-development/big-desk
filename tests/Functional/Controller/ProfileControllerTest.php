<?php

namespace App\Tests\Functional\Controller;

use App\Factory\UserFactory;
use App\Tests\FunctionalTestCase;

class ProfileControllerTest extends FunctionalTestCase
{
    public function testProfile(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();

        $client->request('GET', '/profile');

        self::assertResponseRedirects('/login');

        $client->loginUser($user->object());

        $client->request('GET', '/profile');

        self::assertPageTitleContains('Profile');
        self::assertSelectorTextSame('h1', sprintf('%s\'s Profile', $user->getDisplayName()));
    }
}
