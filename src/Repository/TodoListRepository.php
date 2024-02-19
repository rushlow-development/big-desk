<?php

namespace App\Repository;

use App\Entity\TodoList;

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
    #[\Override]
    protected static function getEntityClassName(): string
    {
        return TodoList::class;
    }
}
