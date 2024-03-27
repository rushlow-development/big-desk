<?php

namespace App\Repository;

use App\Entity\Timer;
use App\Entity\User;

/**
 * @extends AbstractRepository<Timer>
 *
 * @method Timer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timer[]    findAll()
 * @method Timer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimerRepository extends AbstractRepository
{
    /** @return Timer[] */
    public function getTimersForUser(User $user): array
    {
        return $this->findBy(['owner' => $user], ['startedAt' => 'DESC']);
    }

    #[\Override]
    protected static function getEntityClassName(): string
    {
        return Timer::class;
    }
}
