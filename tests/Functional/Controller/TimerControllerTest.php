<?php

namespace App\Tests\Functional\Controller;

use App\Factory\TimeEntryFactory;
use App\Repository\TimeEntryRepository;
use App\Tests\FunctionalTestCase;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class TimerControllerTest extends FunctionalTestCase
{
    public function testCreateTimer(): void
    {
        $client = static::createClient();

        $client->request('POST', '/timer/create');

        /** @var TimeEntryRepository $repository */
        $repository = static::getContainer()->get(TimeEntryRepository::class);

        self::assertCount(1, $repository->findAll());
    }

    public function testStartTimer()
    {
        $client = static::createClient();

        $timeEntry = TimeEntryFactory::createOne(['running' => false]);

        $client->request('POST', '/timer/start/'.$timeEntry->getId());

        self::assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        self::assertJsonStringEqualsJsonString(
            json_encode(['message' => 'OK', 'accumulatedSeconds' => 0]),
            $responseContent
        );
    }

    public function testPauseTimer(): void
    {
        $client = static::createClient();

        $startedAt = new CarbonImmutable();
        Carbon::setTestNow($startedAt);

        $timeEntry = TimeEntryFactory::createOne([
            'startedAt' => $startedAt,
            'running' => true,
        ]);

        Carbon::sleep(120);

        $client->request('POST', '/timer/pause/'.$timeEntry->getId());

        Carbon::setTestNow();

        self::assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();

        self::assertJsonStringEqualsJsonString(
            json_encode(['message' => 'OK', 'accumulatedSeconds' => 120]),
            $responseContent
        );

        self::assertSame(120, (int) $timeEntry->getAccumulatedTime()->totalSeconds);
    }
}
