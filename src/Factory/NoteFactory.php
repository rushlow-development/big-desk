<?php

namespace App\Factory;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Note>
 *
 * @method        Note|Proxy                     create(array|callable $attributes = [])
 * @method static Note|Proxy                     createOne(array $attributes = [])
 * @method static Note|Proxy                     find(object|array|mixed $criteria)
 * @method static Note|Proxy                     findOrCreate(array $attributes)
 * @method static Note|Proxy                     first(string $sortedField = 'id')
 * @method static Note|Proxy                     last(string $sortedField = 'id')
 * @method static Note|Proxy                     random(array $attributes = [])
 * @method static Note|Proxy                     randomOrCreate(array $attributes = [])
 * @method static NoteRepository|RepositoryProxy repository()
 * @method static Note[]|Proxy[]                 all()
 * @method static Note[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Note[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Note[]|Proxy[]                 findBy(array $attributes)
 * @method static Note[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Note[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Note&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static Note&Proxy                     createOne(array $attributes = [])
 * @psalm-method static Note&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static Note&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static Note&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static Note&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static Note&Proxy                     random(array $attributes = [])
 * @psalm-method static Note&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static NoteRepository&RepositoryProxy repository()
 * @psalm-method static Note[]&Proxy[]                 all()
 * @psalm-method static Note[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static Note[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static Note[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static Note[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static Note[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class NoteFactory extends ModelFactory
{
    #[\Override]
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(),
        ];
    }

    public function withContent(): self
    {
        return $this->addState(['content' => self::faker()->paragraph()]);
    }

    #[\Override]
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Note $note): void {})
        ;
    }

    #[\Override]
    protected static function getClass(): string
    {
        return Note::class;
    }
}
