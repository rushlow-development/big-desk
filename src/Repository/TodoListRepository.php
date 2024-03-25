<?php

namespace App\Repository;

use App\Entity\TodoList;
use App\Entity\User;

/**
 * @extends AbstractRepository<TodoList>
 *
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends AbstractRepository
{
    /** @return TodoList[] */
    public function getTodoListForUser(User $user): array
    {
        return $this->findBy(['owner' => $user]);
    }

    #[\Override]
    protected static function getEntityClassName(): string
    {
        return TodoList::class;
    }
}
