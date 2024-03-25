<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\TimeEntryRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    use UserAwareTrait;

    #[Route(name: 'app_main_index')]
    public function index(NoteRepository $noteRepository, TodoListRepository $listRepository, TimeEntryRepository $timeEntryRepository): Response
    {
        $notes = [];

        if (null !== $user = $this->getUser()) {
            $notes = $noteRepository->getNotesForUser($user);
        }

        return $this->render('main.html.twig', [
            'notes' => $notes,
            'todos' => $listRepository->findAll(),
            'timers' => $timeEntryRepository->findBy([], ['startedAt' => 'DESC']),
        ]);
    }
}
