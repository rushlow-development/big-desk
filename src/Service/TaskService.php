<?php

namespace App\Service;

use App\Exception\HttpClientException;
use App\Model\GitHubIssue;
use App\Model\GitHubPullRequest;
use App\Model\GitHubUrlData;
use App\Util\TypeEnum;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @psalm-type ResponseType = array{
 *     data: array{
 *         repository: array<string, array<string, int|string|null>>
 *     }
 * }
 */
final readonly class TaskService
{
    public function __construct(
        private HttpClientInterface $gitHubHttpClient,
    ) {
    }

    /** @throws HttpClientException */
    public function getGitHubDataFromUrl(GitHubUrlData $urlData): GitHubPullRequest|GitHubIssue|null
    {
        if (TypeEnum::PULL_REQUEST === $urlData->type) {
            return $this->queryPullRequest($urlData);
        }

        if (TypeEnum::ISSUE === $urlData->type) {
            return $this->queryIssue($urlData);
        }

        return null;
    }

    private function queryIssue(GitHubUrlData $urlData): ?GitHubIssue
    {
        if (!$payload = file_get_contents($filename = __DIR__.'/../GraphQLRequest/issue.graphql')) {
            throw new HttpClientException(sprintf('Unable to read payload contents for: %s', $filename));
        }

        $response = $this->makeRequest(
            $payload,
            [
                'repository_owner' => $urlData->owner,
                'repository_name' => $urlData->repository,
                'number' => (int) $urlData->identifier,
            ]
        );

        if (!$response) {
            return null;
        }

        return new GitHubIssue(
            uri: $urlData->uri,
            owner: $urlData->owner,
            repo: $urlData->repository,
            number: (int) $urlData->identifier,
            title: (string) $response['data']['repository']['issue']['title'],
        );
    }

    /** @throws HttpClientException */
    private function queryPullRequest(GitHubUrlData $urlData): ?GitHubPullRequest
    {
        if (!$payload = file_get_contents($filename = __DIR__.'/../GraphQLRequest/pull-request.graphql')) {
            throw new HttpClientException(sprintf('Unable to read payload contents for: %s', $filename));
        }

        $response = $this->makeRequest(
            $payload,
            [
                'repository_owner' => $urlData->owner,
                'repository_name' => $urlData->repository,
                'number' => (int) $urlData->identifier,
            ]
        );

        if (!$response) {
            return null;
        }

        return new GitHubPullRequest(
            uri: $urlData->uri,
            owner: $urlData->owner,
            repo: $urlData->repository,
            number: (int) $urlData->identifier,
            title: (string) $response['data']['repository']['pullRequest']['title'],
        );
    }

    /**
     * @param array<string, int|string> $payloadVariables
     *
     * @return ResponseType|null
     *
     * @throws HttpClientException
     */
    private function makeRequest(string $payload, array $payloadVariables): ?array
    {
        $options = [];

        try {
            $options['json'] = ['query' => $payload, 'variables' => json_encode($payloadVariables, \JSON_THROW_ON_ERROR)];

            $response = $this->gitHubHttpClient->request('POST', '/graphql', $options);

            if (200 !== $response->getStatusCode()) {
                return null;
            }

            $data = json_decode(json: $response->getContent(), associative: true, flags: \JSON_THROW_ON_ERROR);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            throw new HttpClientException('GraphQL API Request to GitHub Failed.', previous: $e);
        } catch (\JsonException $e) {
            throw new HttpClientException('Unable to convert payload variables to json.', previous: $e);
        }

        if (empty($data['data']['repository'])) { // @phpstan-ignore-line
            return null;
        }

        /** @var ResponseType $data */
        return $data;
    }
}
