<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Timer;
use App\Entity\User;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class TimeEntryTest extends TestCase
{
    public function testStartTimer(): void
    {
        $user = $this->createMock(User::class);
        $entry = new Timer(new CarbonImmutable(), $user);

        self::assertTrue($entry->isRunning());
        self::assertSame(0, (int) $entry->getAccumulatedTime()->totalSeconds);
    }

    public function testStopTimer(): void
    {
        $user = $this->createMock(User::class);
        $entry = new Timer(new CarbonImmutable(), $user);

        self::assertSame(0.0, $entry->getAccumulatedTime()->totalSeconds);

        sleep(1);

        $entry->stopTimer();

        self::assertFalse($entry->isRunning());
        self::assertSame('1 second', $entry->getAccumulatedTime()->forHumans());

        $entry->startTimer();

        sleep(2);

        $entry->stopTimer();

        self::assertSame('3 seconds', $entry->getAccumulatedTime()->forHumans());
    }
}
