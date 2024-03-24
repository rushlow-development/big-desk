<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/profile', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(name: 'me', methods: ['GET'])]
    public function me(): Response
    {
        return $this->render('profile/me.html.twig');
    }
}
