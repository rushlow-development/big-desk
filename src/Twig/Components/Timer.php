<?php

namespace App\Twig\Components;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Timer
{
    use DefaultActionTrait;

    #[LiveProp(writable: ['name'])]
    public TimeEntry $timer;

    #[LiveProp]
    public bool $isEditing = false;

//    #[LiveAction]
//    public function activateEditing(): void
//    {
//        $this->isEditing = true;
//    }
//
//    #[LiveAction]
//    public function save(TimeEntryRepository $repository): void
//    {
//        dump('Saving....');
//
//        $this->isEditing = false;
//    }

//    #[LiveAction]
//    public function startTimer(TimeEntryRepository $repository): void
//    {
//        $this->timer->startTimer();
//
//        $repository->flush();
//
//        return $this->json([
//            'message' => 'OK',
//            'restartedAt' => $timeEntry->getLastRestartedAt()->timestamp ?? false,
//            'accumulatedSeconds' => $timeEntry->getAccumulatedTime()->totalSeconds,
//        ]);
//    }
}
