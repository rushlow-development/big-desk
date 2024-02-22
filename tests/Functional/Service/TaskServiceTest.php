<?php

namespace App\Tests\Functional\Service;

use App\Tests\FunctionalTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TaskServiceTest extends FunctionalTestCase
{
    public function testSmokeGitHubHttpClient(): void
    {
        static::createKernel()->boot();

        static::getContainer()->set(HttpClientInterface::class, new MockHttpClient());

        $client = static::getContainer()->get('git.hub.http.client.uri_template.inner');

        self::assertInstanceOf(HttpClientInterface::class, $client);

        /** @var array $reflectedDefaultOptions */
        $reflectedDefaultOptions = (new \ReflectionClass($client)) // @phpstan-ignore-line
            ->getProperty('defaultOptionsByRegexp')
            ->getValue($client)
        ;

        self::assertSame('https\://api\.github\.com/', $rUri = array_key_first($reflectedDefaultOptions));

        $expected = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'rushlow-development/big-desk',
            'Authorization' => 'bearer $uperSecretToken',
        ];

        self::assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            $expected,
            $reflectedDefaultOptions[$rUri]['headers'],
            array_keys($expected));
    }
}
