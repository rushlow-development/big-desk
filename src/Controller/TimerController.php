<?php

namespace App\Controller;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/timer', name: 'app_timer_')]
class TimerController extends AbstractController
{
    public function __construct(
        private readonly TimeEntryRepository $repository,
    ) {
    }

    #[Route(path: '/create', name: 'create', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function createTimer(): JsonResponse
    {
        // @TODO CSRF

        $timeEntry = new TimeEntry(startedAt: new CarbonImmutable());

        $timeEntry->startTimer();

        $this->repository->persist($timeEntry, flush: true);

        return $this->json([
            'message' => 'OK',
            'html' => $this->render('time_entry/_timer-card.html.twig', [
                'timer' => $timeEntry,
            ]),
        ]);
    }

    #[Route(path: '/start/{id}', name: 'start', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function startTimer(TimeEntry $timeEntry): JsonResponse
    {
        // @TODO CSRF

        $timeEntry->startTimer();

        $this->repository->flush();

        return $this->json([
            'message' => 'OK',
            'restartedAt' => $timeEntry->getLastRestartedAt()->timestamp,
            'accumulatedSeconds' => $timeEntry->getAccumulatedTime()->totalSeconds,
        ]);
    }

    #[Route(path: '/pause/{id}', name: 'pause', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function pauseTimer(TimeEntry $timeEntry, TimeEntryRepository $repository): JsonResponse
    {
        $timeEntry->stopTimer();

        $repository->flush();

        return $this->json([
            'message' => 'OK',
            'accumulatedSeconds' => $timeEntry->getAccumulatedTime()->totalSeconds,
        ]);
    }
}
