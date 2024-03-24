<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy                     create(array|callable $attributes = [])
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[]                 all()
 * @method static User[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[]                 findBy(array $attributes)
 * @method static User[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        User&Proxy                     create(array|callable $attributes = [])
 * @psalm-method static User&Proxy                     createOne(array $attributes = [])
 * @psalm-method static User&Proxy                     find(object|array|mixed $criteria)
 * @psalm-method static User&Proxy                     findOrCreate(array $attributes)
 * @psalm-method static User&Proxy                     first(string $sortedField = 'id')
 * @psalm-method static User&Proxy                     last(string $sortedField = 'id')
 * @psalm-method static User&Proxy                     random(array $attributes = [])
 * @psalm-method static User&Proxy                     randomOrCreate(array $attributes = [])
 * @psalm-method static UserRepository&RepositoryProxy repository()
 * @psalm-method static User[]&Proxy[]                 all()
 * @psalm-method static User[]&Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @psalm-method static User[]&Proxy[]                 createSequence(iterable|callable $sequence)
 * @psalm-method static User[]&Proxy[]                 findBy(array $attributes)
 * @psalm-method static User[]&Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static User[]&Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'displayName' => self::faker()->userName(),
            'username' => self::faker()->userName(),
            'password' => 'password',
            'roles' => [],
        ];
    }

    protected function initialize(): self
    {
        return $this
             ->afterInstantiate(function (User $user): void {
                 $user->setPassword($this->hasher->hashPassword($user, 'password'));
             })
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
