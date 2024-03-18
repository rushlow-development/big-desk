<?php

namespace App\Controller;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/timer', name: 'app_timer_')]
class TimerController extends AbstractController
{
    #[Route(path: '/start', name: 'new', methods: ['POST'])]
    public function startTimer(TimeEntryRepository $repository): JsonResponse
    {
        // @TODO CSRF

        $timeEntry = new TimeEntry(start: new \DateTimeImmutable());

        $repository->persist($timeEntry, flush: true);

        return $this->json([
            'message' => 'OK',
            'html' => $this->render('time_entry/_timer-card.html.twig', [
                'timer' => $timeEntry,
            ]),
        ]);
    }

    #[Route(path: '/stop/{id}', name: 'stop', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function stopTimer(TimeEntry $timeEntry, TimeEntryRepository $repository): JsonResponse
    {
        $timeEntry->setStopped(new \DateTimeImmutable());

        $repository->flush();

        return $this->json(['message' => 'OK']);
    }
}