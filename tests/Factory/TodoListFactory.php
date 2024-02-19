<?php

namespace App\Tests\Factory;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TodoList>
 *
 * @method        TodoList|Proxy                     create(array|callable $attributes = [])
 * @method static TodoList|Proxy                     createOne(array $attributes = [])
 * @method static TodoList|Proxy                     find(object|array|mixed $criteria)
 * @method static TodoList|Proxy                     findOrCreate(array $attributes)
 * @method static TodoList|Proxy                     first(string $sortedField = 'id')
 * @method static TodoList|Proxy                     last(string $sortedField = 'id')
 * @method static TodoList|Proxy                     random(array $attributes = [])
 * @method static TodoList|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TodoListRepository|RepositoryProxy repository()
 * @method static TodoList[]|Proxy[]                 all()
 * @method static TodoList[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TodoList[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static TodoList[]|Proxy[]                 findBy(array $attributes)
 * @method static TodoList[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TodoList[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        TodoList&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static TodoList&Proxy                     createOne(array $attributes = [])
 * @psalm-method static TodoList&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static TodoList&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static TodoList&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static TodoList&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static TodoList&Proxy                     random(array $attributes = [])
 * @psalm-method static TodoList&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static TodoListRepository&RepositoryProxy repository()
 * @psalm-method static TodoList[]&Proxy[]                 all()
 * @psalm-method static TodoList[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static TodoList[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static TodoList[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static TodoList[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static TodoList[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TodoListFactory extends ModelFactory
{
    #[\Override]
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->sentence(),
            'tasks' => [],
        ];
    }

    public function withTasks(): self
    {
        $tasks = ['Write some code', 'Merge a PR', 'Drink some beer'];

        return $this->addState(['tasks' => $tasks]);
    }

    #[\Override]
    protected static function getClass(): string
    {
        return TodoList::class;
    }
}
