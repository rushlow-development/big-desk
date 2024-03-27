<?php

namespace App\Entity;

use App\Repository\TimerRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimerRepository::class)]
class Timer extends AbstractEntity
{
    public function __construct(
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private CarbonImmutable $startedAt,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User $owner,

        #[ORM\Column]
        private int $accumulatedTime = 0,

        #[ORM\Column]
        private bool $running = true,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        private ?CarbonImmutable $lastRestartedAt = null,

        #[ORM\Column]
        private string $name = '',
    ) {
        parent::__construct();
    }

    public function getStartedAt(): CarbonImmutable
    {
        return $this->startedAt;
    }

    public function getAccumulatedTime(): CarbonInterval
    {
        return (new CarbonInterval())->addSeconds($this->accumulatedTime)->cascade();
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
        if (!$this->running) {
            return $this;
        }

        $started = $this->lastRestartedAt ?? $this->startedAt;

        $interval = $started->diffAsCarbonInterval(absolute: true);

        $this->accumulatedTime = (int) $interval->cascade()->totalSeconds + $this->accumulatedTime;

        $this->running = false;

        return $this;
    }

    public function getTotalSeconds(): int
    {
        if (!$this->running) {
            return $this->accumulatedTime;
        }

        $started = $this->lastRestartedAt ?? $this->startedAt;

        $interval = $started->diffAsCarbonInterval(absolute: true);

        return (int) $interval->cascade()->totalSeconds + $this->accumulatedTime;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }
}
