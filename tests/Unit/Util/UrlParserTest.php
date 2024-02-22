<?php

namespace App\Tests\Unit\Util;

use App\Model\GitHubUrlData;
use App\Util\TypeEnum;
use App\Util\UrlParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlParserTest extends TestCase
{
    #[DataProvider('regexDataProvider')]
    public function testRegex(string $fixture, false|GitHubUrlData $expectedMatch): void
    {
        $result = UrlParser::getGitHubUrlFromText($fixture);

        self::assertEquals($expectedMatch, $result);
    }

    public static function regexDataProvider(): \Generator
    {
        yield [$url = 'https://github.com/owner/repo/pull/1234', new GitHubUrlData('owner', 'repo', TypeEnum::PULL_REQUEST, 1234, $url)];
        yield [$url = 'https://github.com/owner/repo/issues/1234', new GitHubUrlData('owner', 'repo', TypeEnum::ISSUE, 1234, $url)];
        yield [$url = 'https://github.com/owner/repo/releases/tag/v1.5', new GitHubUrlData('owner', 'repo', TypeEnum::RELEASE, 'v1.5', $url)];
        yield ['text before https://github.com/owner/repo/pull/1234', new GitHubUrlData('owner', 'repo', TypeEnum::PULL_REQUEST, 1234, 'https://github.com/owner/repo/pull/1234')];
        yield ['text before https://github.com/owner/repo/issues/1234 text after', new GitHubUrlData('owner', 'repo', TypeEnum::ISSUE, 1234, 'https://github.com/owner/repo/issues/1234')];
        yield ['https://github.com/owner/repo/releases/tag/v1.5 text after', new GitHubUrlData('owner', 'repo', TypeEnum::RELEASE, 'v1.5', 'https://github.com/owner/repo/releases/tag/v1.5')];
        yield ['http://github.com/owner/repo/pull/1234', false];
        yield ['https://github.com/owner/repo/pull/', false];
        yield ['https://github.com/owner/repo/pull', false];
        yield ['https://github.com/owner/repo/', false];
        yield ['https://github.com/owner/repo', false];
        yield ['https://github.com/owner/', false];
        yield ['https://github.com/owner', false];
        yield ['https://github.com/', false];
        yield ['https://github.com', false];
    }
}
