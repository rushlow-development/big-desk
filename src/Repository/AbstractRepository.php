<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template Entity of object
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 *
 * @extends ServiceEntityRepository<Entity>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::getEntityClassName());
    }

    /** @return class-string */
    abstract protected static function getEntityClassName(): string;

    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    public function persist(object $object, bool $flush = false): void
    {
        $this->getEntityManager()->persist($object);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
