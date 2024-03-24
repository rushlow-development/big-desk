<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Dto\UserObject;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, UserRepository $userRepository): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $userObject = new UserObject());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User(
                displayName: (string) $userObject->displayName,
                username: (string) $userObject->username,
                password: ''
            );

            $user->setPassword($userPasswordHasher->hashPassword($user, (string) $userObject->plainPassword));

            $userRepository->persist($user, flush: true);

            $security->login($user, 'form_login', 'main');

            return $this->redirectToRoute('app_profile_me');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
