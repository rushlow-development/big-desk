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

        try {
            $data = json_decode((string) $value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ValueNotConvertible::new($value, 'json', $e->getMessage(), $e);
        }

        return new GitHubPullRequest(
            uri: $data['uri'],
            owner: $data['owner'],
            repo: $data['repo'],
            number: $data['number'],
            title: $data['title'],
        );
    }
}
