<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route(name: 'app_main_index')]
    public function index(NoteRepository $notes): Response
    {
        return $this->render('main.html.twig', [
            'notes' => $notes->findAll(),
        ]);
    }
}
