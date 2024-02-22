<?php

namespace App\Util;

enum TypeEnum: string
{
    case PULL_REQUEST = 'pull';
    case ISSUE = 'issues';
    case RELEASE = 'releases';
}
