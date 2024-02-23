<?php

namespace App\Doctrine\Type;

use App\Model\GitHubPullRequest;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\JsonType;

class GitHubPullRequestType extends JsonType
{
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?GitHubPullRequest
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (\is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (!\is_string($value)) {
            return null;
        }

        try {
            /** @var array<string, string|int|null> $data */
            $data = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ValueNotConvertible::new($value, 'json', $e->getMessage(), $e);
        }

        return new GitHubPullRequest(
            uri: (string) $data['uri'],
            owner: (string) $data['owner'],
            repo: (string) $data['repo'],
            number: (int) $data['number'],
            title: null === $data['title'] ? null : (string) $data['title'],
        );
    }
}
