<?php

namespace App\Controller;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/timer', name: 'app_timer_')]
class TimerController extends AbstractController
{
    #[Route(path: '/start/{id}', name: 'new', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function startTimer(TimeEntryRepository $repository, ?TimeEntry $timeEntry = null): JsonResponse
    {
        // @TODO CSRF

        if (!$timeEntry instanceof TimeEntry) {
            $timeEntry = new TimeEntry(startedAt: new CarbonImmutable());
        }

        $timeEntry->startTimer();

        $repository->persist($timeEntry, flush: true);

        return $this->json([
            'message' => 'OK',
            'html' => $this->render('time_entry/_timer-card.html.twig', [
                'timer' => $timeEntry,
            ]),
        ]);
    }

    #[Route(path: '/pause/{id}', name: 'pause', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function pauseTimer(Request $request, TimeEntry $timeEntry, TimeEntryRepository $repository): JsonResponse
    {
        //        $secondsElapsed = $request->getPayload()->getInt('accumulatedSinceLastStart');

        //        $accumulatedTime = $timeEntry->getAccumulatedTime();
        //        $accumulatedTime->addSeconds($secondsElapsed);

        $timeEntry->stopTimer();

        $repository->flush();

        return $this->json(['message' => 'OK']);
    }

    //    #[Route(path: '/stop/{id}', name: 'stop', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    //    public function stopTimer(TimeEntry $timeEntry, TimeEntryRepository $repository): JsonResponse
    //    {
    //        $timeEntry->setStopped(new CarbonImmutable());
    //
    //        $repository->flush();
    //
    //        return $this->json(['message' => 'OK']);
    //    }
}
