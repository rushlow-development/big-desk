<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/todo', name: 'app_todo_')]
class TodoListController extends AbstractController
{
    public function __construct(
        private readonly TodoListRepository $listRepository,
    ) {
    }

    #[Route(name: 'list_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('todo_list/index.html.twig', [
            'todo_lists' => $this->listRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'list_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(TodoListType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TodoList $list */
            $list = $form->getData();

            $this->listRepository->persist($list);
            $this->listRepository->flush();

            return $this->redirectToRoute('app_todo_list_show', ['id' => $list->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo_list/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'list_show', methods: ['GET'])]
    public function show(TodoList $todoList): Response
    {
        return $this->render('todo_list/show.html.twig', [
            'todo_list' => $todoList,
        ]);
    }

    #[Route('/{id}/edit', name: 'list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TodoList $todoList): Response
    {
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->listRepository->flush();

            return $this->redirectToRoute('app_todo_list_show', ['id' => $todoList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo_list/edit.html.twig', [
            'todo_list' => $todoList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'list_delete', methods: ['POST'])]
    public function delete(Request $request, TodoList $todoList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todoList->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($todoList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_todo_list_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(
        path: '/{id}/task/remove/{number}',
        name: 'task_remove',
        requirements: ['id' => Requirement::UUID, 'number' => Requirement::DIGITS],
        methods: ['POST']
    )]
    public function removeTask(TodoList $todoList, int $number): JsonResponse
    {
        $tasks = $todoList->getTasks();

        if (!\array_key_exists($number, $tasks)) {
            return $this->json(['error' => 'Task index does not exist.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $todoList->removeTask($tasks[$number]);

        $this->listRepository->flush();

        return $this->json(['Ok']);
    }
}
