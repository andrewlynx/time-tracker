<?php

namespace App\Controller;

use App\Constant\PaginatorConstant;
use App\Entity\Task;
use App\Form\DownloadFormType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\Export\FileExportFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Throwable;

/**
 * @Route("/task", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    public function index(Request $request, TaskRepository $taskRepository): Response
    {
        $page = $request->query->get(PaginatorConstant::PAGE) ?? 1;
        $tasks = $taskRepository->getUsersTasks($this->getUser(), $page);

        return $this->render(
            'task/index.html.twig',
            [
                'tasks' => $tasks,
                'forms' => $this->getDeleteFormViews($tasks),
                'download' => $this->getDownloadForm()->createView(),
            ],
        );
    }

    /**
     * @Route("/create", name="create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist(
                $task->setUser($this->getUser())
            );
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', sprintf('Task %s created', $task->getTitle()));

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    /**
     * @Route("/{id}", name="view")
     *
     * @param Task $task
     *
     * @return Response
     */
    public function view(Task $task): Response
    {
        if ($task->getUser() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render(
            'task/view.html.twig',
            [
                'task' => $task,
            ],
        );
    }

    /**
     * @Route("edit/{id}", name="edit")
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Task $task, Request $request): Response
    {
        if ($task->getUser() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(TaskType::class, $task)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', sprintf('Task %s updated', $task->getTitle()));

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    /**
     * @Route("delete/{id}", name="delete")
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function delete(Task $task, Request $request): RedirectResponse
    {
        if ($task->getUser() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->getDeleteForm($task)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->remove($task);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', sprintf('Task %s deleted', $task->getTitle()));

            return $this->redirectToRoute('task_index');
        }

        throw new InvalidCsrfTokenException();
    }

    /**
     * @Route("export", name="export")
     *
     * @param Request           $request
     * @param TaskRepository    $taskRepository
     * @param FileExportFactory $fileExportFactory
     *
     * @return Response
     */
    public function export(
        Request $request,
        TaskRepository $taskRepository,
        FileExportFactory $fileExportFactory
    ): Response {
        $downloadForm = $this->getDownloadForm()->handleRequest($request);
        if ($downloadForm->isSubmitted() && $downloadForm->isValid()) {
            try {
                $startDay = $downloadForm->get('date_from')->getData();
                $endDay = $downloadForm->get('date_to')->getData();
                $tasks = $taskRepository->findByDateRange(
                    $this->getUser(),
                    $startDay,
                    $endDay
                );

                $response = $fileExportFactory->getFileExporter($downloadForm->get('type')->getData())
                    ->setTasks($tasks)
                    ->setAuthor($this->getUser()->getUsername())
                    ->setStartDate($startDay)
                    ->setEndDate($endDay)
                    ->export();
            } catch (Throwable $e) {
                $this->addFlash('error', 'Error occurred trying to generate the report');
            }
        } else {
            $this->addFlash('error', 'Your request cannot be processed correctly');
        }

        return $response ?? $this->redirectToRoute('task_index');
    }

    /**
     * @param Task $task
     *
     * @return Form
     */
    private function getDeleteForm(Task $task): Form
    {
        return $this->get('form.factory')
            ->createNamed(
                $task->getId(),
                'Symfony\Component\Form\Extension\Core\Type\FormType',
                [],
                [
                    'action' => $this->generateUrl('task_delete', ['id' => $task->getId()]),
                ]
            )
            ->add('Delete', SubmitType::class);
    }

    /**
     * @param iterable $tasks
     *
     * @return array
     */
    private function getDeleteFormViews(iterable $tasks): array
    {
        $views = [];
        foreach ($tasks as $task) {
            $views[$task->getId()] = $this->getDeleteForm($task)->createView();
        }

        return $views;
    }

    /**
     * @return FormInterface
     */
    private function getDownloadForm(): FormInterface
    {
        return $this->createForm(
            DownloadFormType::class,
            [
                'action' => $this->get('router')->generate('task_export'),
            ]
        );
    }
}
