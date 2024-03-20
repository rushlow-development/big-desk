<?php

namespace App\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Panther\Client;
use Symfony\Component\Process\Process;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class PantherTestCase extends \Symfony\Component\Panther\PantherTestCase
{
    use Factories;
    use ResetDatabase;

    protected Client $client;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        Process::fromShellCommandline('bin/console asset-map:compile')->run();
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        (new Filesystem())->remove(__DIR__.'/../public/assets');
    }

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createPantherClient();
    }

    public function clickButton(string $selector): void
    {
        $this->client->executeScript(sprintf('document.querySelector(\'%s\').click()', $selector));
    }
}
