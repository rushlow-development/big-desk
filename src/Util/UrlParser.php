<?php

namespace App\Util;

use App\Model\GitHubUrlData;

final readonly class UrlParser
{
    public static function getGitHubUrlFromText(string $text): false|GitHubUrlData
    {
        $expr = "#(?<url>https:\/\/(?:www\.)?github\.com\/(?<owner>.+)\/(?<repo>.+)\/(?<type>issues|pull|releases)\/(?<id>\S+))#";

        $results = [];

        $foundMatch = preg_match(
            pattern: $expr,
            subject: strtolower($text),
            matches: $results,
        );

        if (1 !== $foundMatch || null === $type = TypeEnum::tryFrom($results['type'])) {
            return false;
        }

        if (TypeEnum::RELEASE === $type) {
            $results['id'] = str_replace('tag/', '', $results['id']);
        }

        return new GitHubUrlData(
            owner: $results['owner'],
            repository: $results['repo'],
            type: $type,
            identifier: $results['id'],
            uri: $results['url'],
        );
    }
}
