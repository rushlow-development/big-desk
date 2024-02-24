<?php

namespace App\Model;

use App\Contract\MagicSerializableInterface;

final class GitHubIssue implements MagicSerializableInterface
{
    use SerializableTrait;

    public function __construct(
        public string $uri,
        public string $owner,
        public string $repo,
        public int $number,
        public ?string $title = null,
    ) {
    }
}
