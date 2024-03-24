<?php

namespace App\Tests\Functional\Controller;

use App\Factory\UserFactory;
use App\Model\EncryptedData;
use App\Service\EncryptorService;
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

    public function testUpdateGitHubToken(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();

        // Ensure authentication is required
        $client->request('GET', '/profile/update/token');

        self::assertResponseRedirects('/login');

        // Submit a GitHub "token"
        $client->loginUser($user->object());

        $client->request('GET', '/profile/update/token');
        self::assertPageTitleContains('Update GitHub Token');
        $client->submitForm('Update Token', [
            'git_hub_token[token]' => '1234',
        ]);

        self::assertResponseRedirects('/profile');
        $client->followRedirect();
        self::assertSelectorTextSame('.alert-success', 'GitHub Token Updated!');

        // Ensure the token is encrypted correctly
        $token = $user->getGitHubToken();
        self::assertInstanceOf(EncryptedData::class, $token);

        $container = static::getContainer();
        $encryptor = new EncryptorService(
            $container->getParameter('app.encryption_key'),
            $container->getParameter('kernel.secret'),
        );

        self::assertSame('1234', $encryptor->decryptData($token));

        // Ensure we can remove a token from persistence
        $client->request('GET', '/profile/update/token');
        self::assertPageTitleContains('Update GitHub Token');
        $client->submitForm('Update Token', [
            'git_hub_token[token]' => '',
        ]);

        self::assertResponseRedirects('/profile');
        $client->followRedirect();
        self::assertSelectorTextSame('.alert-warning', 'GitHub Token Removed!');
        self::assertNull($user->getGitHubToken());
    }
}
