<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter implements CacheableVoterInterface
{
    public const string EDIT = 'EDIT';
    public const string DELETE = 'DELETE';
}
