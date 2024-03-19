<?php

namespace App\Repository;

use App\Entity\TimeEntry;

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
    #[\Override]
    protected static function getEntityClassName(): string
    {
        return TimeEntry::class;
    }
}
