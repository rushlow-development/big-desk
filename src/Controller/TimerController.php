<?php

namespace App\Controller;

use App\Entity\Timer;
use App\Repository\TimerRepository;
use App\Security\Voter\TimerVoter;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/timer', name: 'app_timer_')]
class TimerController extends AbstractController
{
    use UserAwareTrait;

    public function __construct(
        private readonly TimerRepository $repository,
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/create', name: 'create', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function createTimer(): JsonResponse
    {
        // @TODO CSRF

        $timeEntry = new Timer(
            startedAt: new CarbonImmutable(),
            owner: $this->getAuthenticatedUser(),
        );

        $timeEntry->startTimer();

        $this->repository->persist($timeEntry, flush: true);

        return $this->json([
            'message' => 'OK',
            'html' => $this->render('timer/_timer-card.html.twig', [
                'timer' => $timeEntry,
            ]),
        ]);
    }

    #[IsGranted(TimerVoter::EDIT, 'timeEntry')]
    #[Route(path: '/start/{id}', name: 'start', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function startTimer(Timer $timeEntry): JsonResponse
    {
        // @TODO CSRF

        $timeEntry->startTimer();

        $this->repository->flush();

        return $this->json([
            'message' => 'OK',
            'restartedAt' => $timeEntry->getLastRestartedAt()->timestamp ?? false,
            'accumulatedSeconds' => $timeEntry->getAccumulatedTime()->totalSeconds,
        ]);
    }

    #[IsGranted(TimerVoter::EDIT, 'timeEntry')]
    #[Route(path: '/pause/{id}', name: 'pause', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function pauseTimer(Timer $timeEntry): JsonResponse
    {
        $timeEntry->stopTimer();

        $this->repository->flush();

        return $this->json([
            'message' => 'OK',
            'accumulatedSeconds' => $timeEntry->getAccumulatedTime()->totalSeconds,
        ]);
    }

    #[IsGranted(TimerVoter::DELETE, 'timeEntry')]
    #[Route(path: '/remove/{id}', name: 'remove', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function remove(Timer $timeEntry): JsonResponse
    {
        $this->repository->remove($timeEntry, flush: true);

        return $this->json([
            'message' => 'OK',
        ]);
    }
}
