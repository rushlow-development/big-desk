<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Exception\HttpClientException;
use App\Form\TodoListType;
use App\Model\GitHubIssue;
use App\Model\GitHubPullRequest;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use App\Service\TaskService;
use App\Util\UrlParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/todo', name: 'app_todo_')]
final class TodoListController extends AbstractController
{
    public function __construct(
        private readonly TodoListRepository $listRepository,
    ) {
    }

    #[Route('/new', name: 'list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskService $taskService, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TodoListType::class, new TodoList(''));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TodoList $list */
            $list = $form->getData();

            foreach ($list->getTasks() as $task) {
                $this->checkForGitHub($task, $taskService);
            }

            $this->listRepository->persist($list, true);

            dump($list);

            return $this->redirectToRoute('app_main_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo_list/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'list_edit', requirements: ['id' => Requirement::UUID], methods: ['GET', 'POST'])]
    public function edit(Request $request, TodoList $todoList, TaskService $taskService): Response
    {
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($todoList->getTasks() as $task) {
                $this->checkForGitHub($task, $taskService);
            }

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

    protected function checkForGitHub(Task $task, TaskService $taskService): void
    {
        $gitHubLink = UrlParser::getGitHubUrlFromText($task->getName());

        if (false === $gitHubLink) {
            return;
        }

        try {
            // We want to query GitHub to grab the information about the link
            $data = $taskService->getGitHubDataFromUrl($gitHubLink);
        } catch (HttpClientException) {
            // @TODO - Do something with this in the future...
            return;
        }

        if ($data instanceof GitHubIssue || $data instanceof GitHubPullRequest) {
            $task->addGitHub($data);
        }
    }
}
