<?php

namespace App\Factory;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Task>
 *
 * @method        Task|Proxy                     create(array|callable $attributes = [])
 * @method static Task|Proxy                     createOne(array $attributes = [])
 * @method static Task|Proxy                     find(object|array|mixed $criteria)
 * @method static Task|Proxy                     findOrCreate(array $attributes)
 * @method static Task|Proxy                     first(string $sortedField = 'id')
 * @method static Task|Proxy                     last(string $sortedField = 'id')
 * @method static Task|Proxy                     random(array $attributes = [])
 * @method static Task|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TaskRepository|RepositoryProxy repository()
 * @method static Task[]|Proxy[]                 all()
 * @method static Task[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Task[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Task[]|Proxy[]                 findBy(array $attributes)
 * @method static Task[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Task[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Task&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static Task&Proxy                     createOne(array $attributes = [])
 * @psalm-method static Task&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static Task&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static Task&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static Task&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static Task&Proxy                     random(array $attributes = [])
 * @psalm-method static Task&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static TaskRepository&RepositoryProxy repository()
 * @psalm-method static Task[]&Proxy[]                 all()
 * @psalm-method static Task[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static Task[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static Task[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static Task[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static Task[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TaskFactory extends ModelFactory
{
    #[\Override]
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(255),
            'todoList' => TodoListFactory::new(),
            'github' => new ArrayCollection(),
        ];
    }

    #[\Override]
    protected static function getClass(): string
    {
        return Task::class;
    }
}
