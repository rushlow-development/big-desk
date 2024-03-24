<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use App\Tests\FunctionalTestCase;

class RegistrationControllerTest extends FunctionalTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/register');
        $client->submitForm('Register', [
            'registration_form[displayName]' => 'Jim Bob',
            'registration_form[username]' => 'testUser1234',
            'registration_form[plainPassword]' => 'secretPassword1234',
        ]);

        self::assertResponseIsSuccessful();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        self::assertCount(1, $userRepository->findAll());
        self::assertPageTitleContains('Profile');
        self::assertSelectorTextContains('h1', 'Jim Bob\'s Profile');
    }
}
