<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Profile\GitHubTokenType;
use App\Repository\UserRepository;
use App\Service\EncryptorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route(path: '/update/token', name: 'update_github_token', methods: ['GET', 'POST'])]
    public function updateGitHubToken(Request $request, EncryptorService $encryptor, UserRepository $userRepository): Response
    {
        $form = $this->createForm(GitHubTokenType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @phpstan-ignore-next-line */
            $token = trim((string) $form->get('token')->getData());

            /** @var User $user */
            $user = $this->getUser();

            if (empty($token)) {
                $this->addFlash('warning', 'GitHub Token Removed!');

                $user->setGitHubToken(null);
                $userRepository->flush();

                return $this->redirectToRoute('app_profile_me');
            }

            $encryptedData = $encryptor->encryptData($token);
            $user->setGitHubToken($encryptedData);
            $userRepository->flush();

            $this->addFlash('success', 'GitHub Token Updated!');

            return $this->redirectToRoute('app_profile_me');
        }

        return $this->render('profile/edit.token.html.twig', [
            'form' => $form,
        ]);
    }
}
