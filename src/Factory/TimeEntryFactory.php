<?php

namespace App\Factory;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Carbon\CarbonImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TimeEntry>
 *
 * @method        TimeEntry|Proxy                     create(array|callable $attributes = [])
 * @method static TimeEntry|Proxy                     createOne(array $attributes = [])
 * @method static TimeEntry|Proxy                     find(object|array|mixed $criteria)
 * @method static TimeEntry|Proxy                     findOrCreate(array $attributes)
 * @method static TimeEntry|Proxy                     first(string $sortedField = 'id')
 * @method static TimeEntry|Proxy                     last(string $sortedField = 'id')
 * @method static TimeEntry|Proxy                     random(array $attributes = [])
 * @method static TimeEntry|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TimeEntryRepository|RepositoryProxy repository()
 * @method static TimeEntry[]|Proxy[]                 all()
 * @method static TimeEntry[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static TimeEntry[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static TimeEntry[]|Proxy[]                 findBy(array $attributes)
 * @method static TimeEntry[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static TimeEntry[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        TimeEntry&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static TimeEntry&Proxy                     createOne(array $attributes = [])
 * @psalm-method static TimeEntry&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static TimeEntry&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static TimeEntry&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static TimeEntry&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static TimeEntry&Proxy                     random(array $attributes = [])
 * @psalm-method static TimeEntry&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static TimeEntryRepository&RepositoryProxy repository()
 * @psalm-method static TimeEntry[]&Proxy[]                 all()
 * @psalm-method static TimeEntry[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static TimeEntry[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static TimeEntry[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static TimeEntry[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static TimeEntry[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TimeEntryFactory extends ModelFactory
{
    #[\Override]
    protected function getDefaults(): array
    {
        return [
            'startedAt' => CarbonImmutable::createFromInterface(self::faker()->dateTime()),
            'owner' => UserFactory::new(),
        ];
    }

    public function notRunning(): self
    {
        return $this->addState(['running' => false]);
    }

    public function isRunning(?CarbonImmutable $startedAt = null): self
    {
        $state = ['running' => true];

        if ($startedAt) {
            $state['startedAt'] = $startedAt;
        }

        return $this->addState($state);
    }

    #[\Override]
    protected static function getClass(): string
    {
        return TimeEntry::class;
    }
}
