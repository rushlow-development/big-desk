<?php

namespace App\Tests\Functional\Controller;

use App\Factory\TimeEntryFactory;
use App\Factory\UserFactory;
use App\Repository\TimeEntryRepository;
use App\Tests\FunctionalTestCase;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TimerControllerTest extends FunctionalTestCase
{
    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateTimer(): void
    {
        $user = UserFactory::createOne();

        // Deny - Not Authenticated
        $this->client->request('POST', $this->getPath('/create'));
        self::assertResponseRedirects('/login');

        // Allow authenticated user to create a timer
        $this->client->loginUser($user->object());
        $this->client->request('POST', $this->getPath('/create'));

        /** @var TimeEntryRepository $repository */
        $repository = static::getContainer()->get(TimeEntryRepository::class);

        self::assertCount(1, $repository->findAll());
    }

    public function testStartTimer(): void
    {
        $user = UserFactory::createOne();
        $timeEntry = TimeEntryFactory::new(['owner' => $user])->notRunning()->create();
        $path = $this->getPath(sprintf('/start/%s', $timeEntry->getId()));

        // Deny - Not Authenticated
        $this->client->request('POST', $path);
        self::assertResponseRedirects('/login');

        // Deny - Not Owner of Timer
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('POST', $path);
        self::assertResponseStatusCodeSame(403);

        // Allow Owner to start Timer
        $this->client->loginUser($user->object());
        $this->client->request('POST', $path);

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString(
            /** @phpstan-ignore-next-line  */
            json_encode(['message' => 'OK', 'accumulatedSeconds' => 0, 'restartedAt' => $timeEntry->getLastRestartedAt()->timestamp]),
            /** @phpstan-ignore-next-line  */
            $this->client->getResponse()->getContent()
        );
    }

    public function testPauseTimer(): void
    {
        $user = UserFactory::createOne();

        Carbon::setTestNow($startedAt = new CarbonImmutable());
        $timeEntry = TimeEntryFactory::new(['owner' => $user])->isRunning($startedAt)->create();
        Carbon::sleep(120);

        $path = $this->getPath(sprintf('/pause/%s', $timeEntry->getId()));

        // Deny - Not Authenticated
        $this->client->request('POST', $path);
        self::assertResponseRedirects('/login');

        // Deny - Not Owner of Timer
        $this->client->loginUser(UserFactory::createOne()->object());
        $this->client->request('POST', $path);
        self::assertResponseStatusCodeSame(403);

        // Allow Owner to start Timer
        $this->client->loginUser($user->object());
        $this->client->request('POST', $path);

        Carbon::setTestNow();

        self::assertResponseIsSuccessful();

        /** @phpstan-ignore-next-line  */
        self::assertJsonStringEqualsJsonString(json_encode(['message' => 'OK', 'accumulatedSeconds' => 120]), $this->client->getResponse()->getContent());
        self::assertSame(120, (int) $timeEntry->getAccumulatedTime()->totalSeconds);
    }

    private function getPath(?string $suffix = null): string
    {
        $path = '/timer';

        return null === $suffix ? $path : sprintf('%s%s', $path, $suffix);
    }
}
