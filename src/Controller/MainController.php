<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\TimerRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    use UserAwareTrait;

    #[Route(name: 'app_main_index')]
    public function index(NoteRepository $noteRepository, TodoListRepository $listRepository, TimerRepository $timerRepository): Response
    {
        $notes = [];
        $todoLists = [];
        $timers = [];

        if (null !== $user = $this->getUser()) {
            $notes = $noteRepository->getNotesForUser($user);
            $todoLists = $listRepository->getTodoListForUser($user);
            $timers = $timerRepository->getTimersForUser($user);
        }

        return $this->render('main.html.twig', [
            'notes' => $notes,
            'todos' => $todoLists,
            'timers' => $timers,
        ]);
    }
}
