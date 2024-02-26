<?php

namespace App\Model;

use RD\SerializeTypeBundle\SerializableTypeInterface;
use RD\SerializeTypeBundle\SerializableTypeTrait;

final class GitHubPullRequest implements SerializableTypeInterface
{
    use SerializableTypeTrait;

    public function __construct(
        public string $uri,
        public string $owner,
        public string $repo,
        public int $number,
        public ?string $title = null,
    ) {
    }
}
