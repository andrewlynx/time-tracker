<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route("/task", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->getUsersTasks($this->getUser());

        return $this->render(
            'task/index.html.twig',
            [
                'tasks' => $tasks,
                'forms' => $this->getDeleteFormViews($tasks),
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

    private function getDeleteFormViews(iterable $tasks): array
    {
        $views = [];
        foreach ($tasks as $task) {
            $views[$task->getId()] = $this->getDeleteForm($task)->createView();
        }

        return $views;
    }
}