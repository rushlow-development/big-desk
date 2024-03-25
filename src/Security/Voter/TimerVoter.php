<?php

namespace App\Security\Voter;

use App\Entity\TimeEntry;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TimerVoter extends AbstractVoter
{
    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof TimeEntry && \in_array($attribute, [
            self::EDIT,
            self::DELETE,
        ]);
    }

    /** @param TimeEntry $subject */
    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::EDIT, self::DELETE => $user === $subject->getOwner(),
            default => false,
        };
    }
}
