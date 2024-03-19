<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry extends AbstractEntity
{
    public function __construct(
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private CarbonImmutable $startedAt,

        #[ORM\Column(type: Types::DATEINTERVAL, nullable: true)]
        private CarbonInterval $accumulatedTime = new CarbonInterval(),

        #[ORM\Column]
        private bool $running = true,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private ?CarbonImmutable $lastRestartedAt = null,
    ) {
        parent::__construct();
    }

    public function getStartedAt(): CarbonImmutable
    {
        return $this->startedAt;
    }

    public function getAccumulatedTime(): CarbonInterval
    {
        return $this->accumulatedTime;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function getLastRestartedAt(): ?CarbonImmutable
    {
        return $this->lastRestartedAt;
    }

    public function startTimer(): self
    {
        $this->running = true;

        $this->lastRestartedAt = new CarbonImmutable();

        return $this;
    }

    public function stopTimer(): self
    {
        $started = $this->lastRestartedAt ?? $this->startedAt;

        $diff = $started->diffAsCarbonInterval(absolute: true);
        $this->accumulatedTime
            ->addSeconds($diff->totalSeconds)
            ->cascade()
        ;

        $this->running = false;

        return $this;
    }
}
