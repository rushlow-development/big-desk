<?php

namespace App\Factory;

use App\Entity\Timer;
use App\Repository\TimerRepository;
use Carbon\CarbonImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Timer>
 *
 * @method        Timer|Proxy                     create(array|callable $attributes = [])
 * @method static Timer|Proxy                     createOne(array $attributes = [])
 * @method static Timer|Proxy                     find(object|array|mixed $criteria)
 * @method static Timer|Proxy                     findOrCreate(array $attributes)
 * @method static Timer|Proxy                     first(string $sortedField = 'id')
 * @method static Timer|Proxy                     last(string $sortedField = 'id')
 * @method static Timer|Proxy                     random(array $attributes = [])
 * @method static Timer|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TimerRepository|RepositoryProxy repository()
 * @method static Timer[]|Proxy[]                 all()
 * @method static Timer[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Timer[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Timer[]|Proxy[]                 findBy(array $attributes)
 * @method static Timer[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Timer[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Timer&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static Timer&Proxy                     createOne(array $attributes = [])
 * @psalm-method static Timer&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static Timer&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static Timer&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static Timer&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static Timer&Proxy                     random(array $attributes = [])
 * @psalm-method static Timer&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static TimerRepository&RepositoryProxy repository()
 * @psalm-method static Timer[]&Proxy[]                 all()
 * @psalm-method static Timer[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static Timer[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static Timer[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static Timer[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static Timer[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TimerFactory extends ModelFactory
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
        return Timer::class;
    }
}
