<?php

namespace App\Twig\Components;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Timer
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: ['name'])]
    public TimeEntry $timer;

    #[LiveProp]
    public bool $isEditing = false;

    #[LiveAction]
    public function startTimer(TimeEntryRepository $repository): void
    {
        $this->timer->startTimer();

        $repository->flush();

        $this->dispatchBrowserEvent('timer:start:'.$this->timer->getId(), [
            'totalSeconds' => $this->timer->getTotalSeconds(),
        ]);
    }

    #[LiveAction]
    public function stopTimer(TimeEntryRepository $repository): void
    {
        $this->timer->stopTimer();

        $repository->flush();

        $this->dispatchBrowserEvent('timer:stop:'.$this->timer->getId());
    }

    #[LiveAction]
    public function editMode(): void
    {
        if ($this->isEditing) {
            $this->isEditing = false;
        } else {
            $this->isEditing = true;
        }
    }

    #[LiveAction]
    public function saveName(TimeEntryRepository $repository): void
    {
        $repository->flush();

        $this->isEditing = false;
    }
}
