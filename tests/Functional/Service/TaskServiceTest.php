<?php

namespace App\Tests\Functional\Service;

use App\Model\GitHubIssue;
use App\Model\GitHubPullRequest;
use App\Model\GitHubUrlData;
use App\Service\TaskService;
use App\Tests\FunctionalTestCase;
use App\Util\TypeEnum;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TaskServiceTest extends FunctionalTestCase
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

    public function testGetGitHubDataFromUrlWithPullRequest(): void
    {
        $urlDataFixture = new GitHubUrlData(
            owner: 'rushlow',
            repository: 'big-desk',
            type: TypeEnum::PULL_REQUEST,
            identifier: '100', // Set as a string to ensure it's converted to an int
            uri: 'https://github.com/rushlow/big-desk/pulls/100'
        );

        $expected = new GitHubPullRequest(
            uri: 'https://github.com/rushlow/big-desk/pulls/100',
            owner: 'rushlow',
            repo: 'big-desk',
            number: 100,
            title: 'The title of my PR.',
        );

        $mockResponse = new MockResponse((string) file_get_contents(__DIR__.'/GraphQLResponseFixture/pull-request.200.json'));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $service = new TaskService($mockHttpClient);
        $result = $service->getGitHubDataFromUrl($urlDataFixture);

        self::assertEquals($expected, $result);

        self::assertSame('https://example.com/graphql', $mockResponse->getRequestUrl());
        self::assertSame('POST', $mockResponse->getRequestMethod());

        $requestBody = $mockResponse->getRequestOptions()['body'];

        self::assertStringContainsString('pullRequest(', $requestBody);
        self::assertStringContainsString('\u0022repository_owner\u0022:\u0022rushlow\u0022', $requestBody);
        self::assertStringContainsString('\u0022repository_name\u0022:\u0022big-desk\u0022', $requestBody);
        self::assertStringContainsString('\u0022number\u0022:100', $requestBody);
    }

    public function testGetGitHubDataFromUrlWithIssue(): void
    {
        $urlDataFixture = new GitHubUrlData(
            owner: 'rushlow',
            repository: 'big-desk',
            type: TypeEnum::ISSUE,
            identifier: '100', // Set as a string to ensure it's converted to an int
            uri: 'https://github.com/rushlow/big-desk/issues/100'
        );

        $expected = new GitHubIssue(
            uri: 'https://github.com/rushlow/big-desk/issues/100',
            owner: 'rushlow',
            repo: 'big-desk',
            number: 100,
            title: 'Add a maker for Doctrine migrations',
        );

        $mockResponse = new MockResponse((string) file_get_contents(__DIR__.'/GraphQLResponseFixture/issue.200.json'));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $service = new TaskService($mockHttpClient);
        $result = $service->getGitHubDataFromUrl($urlDataFixture);

        self::assertEquals($expected, $result);

        self::assertSame('https://example.com/graphql', $mockResponse->getRequestUrl());
        self::assertSame('POST', $mockResponse->getRequestMethod());

        $requestBody = $mockResponse->getRequestOptions()['body'];

        self::assertStringContainsString('issue(', $requestBody);
        self::assertStringContainsString('\u0022repository_owner\u0022:\u0022rushlow\u0022', $requestBody);
        self::assertStringContainsString('\u0022repository_name\u0022:\u0022big-desk\u0022', $requestBody);
        self::assertStringContainsString('\u0022number\u0022:100', $requestBody);
    }
}
