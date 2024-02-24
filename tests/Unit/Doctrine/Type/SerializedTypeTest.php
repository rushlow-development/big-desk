<?php

namespace App\Tests\Unit\Doctrine\Type;

use App\Doctrine\Type\SerializedType;
use App\Model\GitHubIssue;
use App\Tests\FunctionalTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Zenstruck\Foundry\Test\Factories;

class SerializedTypeTest extends FunctionalTestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function testConvertToPhp(): void
    {
        $fixture = new GitHubIssue(
            uri: 'http://example.com',
            owner: 'rushlow-development',
            repo: 'big-desk',
            number: 100,
            title: 'This is the title.'
        );

        $doctrineType = new SerializedType();
        $serialized = $doctrineType->convertToDatabaseValue($fixture, $this->createMock(AbstractPlatform::class));

        self::assertNotNull($serialized);

        $result = $doctrineType->convertToPHPValue($serialized, $this->createMock(AbstractPlatform::class));

        self::assertInstanceOf(GitHubIssue::class, $result);

        self::assertEquals($fixture, $result);
    }

    public function testConvertToPhpWithCollection(): void
    {
        $collectionItem = new GitHubIssue(
            uri: 'http://example.com',
            owner: 'rushlow-development',
            repo: 'big-desk',
            number: 100,
            title: 'This is the title.'
        );

        $fixture = new ArrayCollection();
        $fixture->add($collectionItem);

        $doctrineType = new SerializedType();
        $serialized = $doctrineType->convertToDatabaseValue($fixture, $this->createMock(AbstractPlatform::class));

        self::assertNotNull($serialized);

        $result = $doctrineType->convertToPHPValue($serialized, $this->createMock(AbstractPlatform::class));

        self::assertInstanceOf(ArrayCollection::class, $result);

        self::assertEquals($fixture, $result);
    }
}
