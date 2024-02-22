<?php

namespace App\Repository;

use App\Entity\Task;

/**
 * @extends AbstractRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends AbstractRepository
{
    #[\Override]
    protected static function getEntityClassName(): string
    {
        return Task::class;
    }
}
