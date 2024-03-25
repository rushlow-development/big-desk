<?php

namespace App\Controller;

use App\Entity\User;

trait UserAwareTrait
{
    public function getUser(): ?User
    {
        $user = parent::getUser();

        if (null === $user || $user instanceof User) {
            return $user;
        }

        throw new \RuntimeException('Unknown user object');
    }

    protected function getAuthenticatedUser(): User
    {
        if (($user = $this->getUser()) instanceof User) {
            return $user;
        }

        throw new \RuntimeException('Unknown user object');
    }
}
