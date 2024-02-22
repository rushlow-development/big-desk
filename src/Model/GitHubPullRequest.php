<?php

namespace App\Model;

final class GitHubPullRequest
{
    public function __construct(
        public string $uri,
        public string $owner,
        public string $repo,
        public int $number,
        public ?string $title = null,
    ) {
    }
}
