<?php

namespace App\Repository;

use App\Entity\Note;

/**
 * @extends AbstractRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends AbstractRepository
{
    #[\Override]
    protected static function getEntityClassName(): string
    {
        return Note::class;
    }
}
