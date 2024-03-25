<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\Dto\NoteObject;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Security\Voter\NoteVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/note', name: 'app_note_')]
class NoteController extends AbstractController
{
    use UserAwareTrait;

    public function __construct(
        private readonly NoteRepository $noteRepository,
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(NoteType::class, $dto = new NoteObject());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note = Note::createFromDto($dto, $this->getAuthenticatedUser());

            $this->noteRepository->persist($note, true);

            return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted(NoteVoter::EDIT, 'note')]
    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note): Response
    {
        $dto = new NoteObject($note->getTitle(), $note->getContent());

        $form = $this->createForm(NoteType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->refreshFromDto($dto);

            $this->noteRepository->flush();

            return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[IsGranted(NoteVoter::DELETE, 'note')]
    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), (string) $request->request->get('_token'))) {
            $this->noteRepository->remove($note, true);
        }

        return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
    }
}
