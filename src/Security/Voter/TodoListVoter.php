<?php

namespace App\Security\Voter;

use App\Entity\TodoList;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TodoListVoter extends AbstractVoter
{
    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof TodoList && \in_array($attribute, [
            self::EDIT,
            self::DELETE,
        ]);
    }

    /** @param TodoList $subject */
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
