<?php

namespace App\Security\Voter;

use App\Entity\Note;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class NoteVoter extends AbstractVoter
{
    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Note && \in_array($attribute, [
            self::EDIT,
            self::DELETE,
        ]);
    }

    /** @param Note $subject */
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
