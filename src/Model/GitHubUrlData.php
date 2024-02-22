<?php

namespace App\Model;

use App\Util\TypeEnum;

final class GitHubUrlData
{
    public function __construct(
        public string $owner,
        public string $repository,
        public TypeEnum $type,
        public int|string $identifier,
        public string $uri,
    ) {
    }
}
