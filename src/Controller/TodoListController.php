<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
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

    #[Route('/new', name: 'list_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(TodoListType::class, new TodoList(''));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TodoList $list */
            $list = $form->getData();

            $this->listRepository->persist($list, true);

            return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo_list/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'list_edit', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function edit(Request $request, TodoList $todoList): Response
    {
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->listRepository->flush();

            return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo_list/edit.html.twig', [
            'todo_list' => $todoList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'list_delete', requirements: ['id' => Requirement::UUID], methods: ['POST'])]
    public function delete(Request $request, TodoList $todoList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todoList->getId(), (string) $request->request->get('_token'))) {
            $this->listRepository->remove($todoList, true);
        }

        return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route(
        path: '/{id}/task/remove/{task}',
        name: 'task_remove',
        requirements: ['id' => Requirement::UUID, 'task' => Requirement::UUID],
        methods: ['POST']
    )]
    public function removeTask(TodoList $todoList, Task $task): JsonResponse
    {
        $todoList->removeTask($task);

        $this->listRepository->flush();

        return $this->json(['Ok']);
    }
}
