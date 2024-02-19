<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Panther\Client;
use Zenstruck\Foundry\ChainManagerRegistry;
use Zenstruck\Foundry\Configuration;
use Zenstruck\Foundry\Exception\FoundryBootException;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Test\DatabaseResetter;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\LazyManagerRegistry;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\TestState;

abstract class PantherTestCase extends \Symfony\Component\Panther\PantherTestCase
{
    use Factories;
    use ResetDatabase;

    protected Client $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createPantherClient();
    }

    public function clickButton(string $selector): void
    {
        $this->client->executeScript(sprintf('document.querySelector(\'%s\').click()', $selector));
    }

    #[Before]
    public static function _setUpFactories(): void
    {
        if (!is_subclass_of(static::class, KernelTestCase::class)) {
            TestState::bootFoundryForUnitTest();

            return;
        }

        $kernel = static::createKernel();
        $kernel->boot();

        TestState::bootFromContainer($kernel->getContainer());
        Factory::configuration()->setManagerRegistry(
            new LazyManagerRegistry(static function (): ChainManagerRegistry {
                if (!static::$booted) {
                    static::bootKernel();
                }

                return TestState::initializeChainManagerRegistry(static::$kernel->getContainer()); // @phpstan-ignore-line
            }
            )
        );

        $kernel->shutdown();
    }

    #[After]
    public static function _tearDownFactories(): void
    {
        try {
            Factory::configuration()->enablePersist();
        } catch (FoundryBootException) {
        }

        TestState::shutdownFoundry();
    }

    public function disablePersist(): void
    {
        Factory::configuration()->disablePersist();
    }

    public function enablePersist(): void
    {
        Factory::configuration()->enablePersist();
    }

    #[BeforeClass]
    public static function _resetDatabase(): void
    {
        if (!is_subclass_of(static::class, KernelTestCase::class)) {
            throw new \RuntimeException(sprintf('The "%s" trait can only be used on TestCases that extend "%s".', __TRAIT__, KernelTestCase::class));
        }

        $kernel = static::createKernel();
        $kernel->boot();

        try {
            $kernel->getBundle('ZenstruckFoundryBundle');
        } catch (\InvalidArgumentException) {
            trigger_deprecation('zenstruck\foundry', '1.23', 'Usage of ResetDatabase trait without Foundry bundle is deprecated and will create an error in 2.0.');
        }

        if (self::shouldReset($kernel)) {
            DatabaseResetter::resetDatabase($kernel, false);
        }

        $kernel->shutdown();
    }

    #[Before]
    public static function _resetSchema(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();

        DatabaseResetter::resetSchema($kernel);

        $kernel->shutdown();
    }

    private static function shouldReset(KernelInterface $kernel): bool
    {
        if (isset($_SERVER['FOUNDRY_DISABLE_DATABASE_RESET'])) {
            trigger_deprecation('zenstruck\foundry', '1.23', 'Usage of environment variable "FOUNDRY_DISABLE_DATABASE_RESET" is deprecated. Please use bundle configuration: "database_resetter.disabled: true".');

            return false;
        }

        $configuration = self::getConfiguration($kernel->getContainer());

        if ($configuration && !$configuration->isDatabaseResetEnabled()) {
            return false;
        }

        return !DatabaseResetter::hasBeenReset();
    }

    private static function getConfiguration(ContainerInterface $container): ?Configuration
    {
        if ($container->has('.zenstruck_foundry.configuration')) { // @phpstan-ignore-line
            return $container->get('.zenstruck_foundry.configuration'); // @phpstan-ignore-line
        }

        trigger_deprecation('zenstruck\foundry', '1.23', 'Usage of foundry without the bundle is deprecated and will not be possible anymore in 2.0.');

        return null;
    }
}
