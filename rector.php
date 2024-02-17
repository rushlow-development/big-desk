<?php

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $config): void {
    $config->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $config->bootstrapFiles([
        __DIR__.'/vendor/autoload.php',
        __DIR__.'/tools/rector/vendor/autoload.php',
    ]);

    $config->symfonyContainerXml(__DIR__.'/var/cache/test/App_KernelTestDebugContainer.xml');
    $config->symfonyContainerPhp(__DIR__.'/tests/rector-bootstrap.php');

    $config->sets([
        LevelSetList::UP_TO_PHP_83,
        SetList::DEAD_CODE,
        SymfonySetList::SYMFONY_64,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_100,
    ]);

    $config->skip([
        PreferPHPUnitThisCallRector::class
    ]);
};
