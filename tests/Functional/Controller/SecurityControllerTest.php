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

        $crawler = $client->submitForm('Sign in', [
            '_username' => 'Some User',
            '_password' => 'My Password',
        ]);

        self::assertSelectorCount(1, '.alert');
        self::assertSame('Invalid credentials.', $crawler->filter('.alert-danger')->first()->text());

        $user = UserFactory::createOne();

        $client->submitForm('Sign in', [
            '_username' => $user->getUserIdentifier(),
            '_password' => 'password',
        ]);

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('home');
    }
}
