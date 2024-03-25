<?php

namespace App\Repository;

use App\Entity\TimeEntry;
use App\Entity\User;

/**
 * @extends AbstractRepository<TimeEntry>
 *
 * @method TimeEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeEntry[]    findAll()
 * @method TimeEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeEntryRepository extends AbstractRepository
{
    /** @return TimeEntry[] */
    public function getTimersForUser(User $user): array
    {
        /** @phpstan-ignore-next-line */
        return $this->findBy(['owner' => $user], ['startedAt' => 'DESC']);
    }

    #[\Override]
    protected static function getEntityClassName(): string
    {
        return TimeEntry::class;
    }
}
