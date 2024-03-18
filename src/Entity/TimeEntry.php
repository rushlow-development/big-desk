<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry extends AbstractEntity
{
    public function __construct(
        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        private \DateTimeImmutable $start,

        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
        private ?\DateTimeImmutable $stopped = null,
    ) {
        parent::__construct();
    }

    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getStopped(): ?\DateTimeImmutable
    {
        return $this->stopped;
    }

    public function setStopped(?\DateTimeImmutable $stopped): static
    {
        $this->stopped = $stopped;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return null !== $this->stopped ? $this->start->diff($this->stopped) : null;
    }
}
