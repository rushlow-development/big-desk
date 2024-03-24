<?php

namespace App\Tests\Functional\Controller;

use App\Factory\UserFactory;
use App\Tests\FunctionalTestCase;

class SecurityControllerTest extends FunctionalTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/login');

        $client->submitForm('Sign in', [
            '_username' => 'Some User',
            '_password' => 'My Password',
        ]);

        self::assertSelectorCount(1, '.alert');
        self::assertSelectorTextSame('.alert-danger', 'Invalid credentials.');

        $user = UserFactory::createOne();

        $client->submitForm('Sign in', [
            '_username' => $user->getUserIdentifier(),
            '_password' => 'password',
        ]);

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('home');
    }
}
