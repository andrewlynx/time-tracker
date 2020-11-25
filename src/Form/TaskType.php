<?php

namespace App\Form;

use App\Entity\Task;
use App\Form\Extensions\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Task $task */
        $task = $options['data'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Task Title',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comment',
                'required' => false,
            ])
            ->add('date', DatePickerType::class, [
                'label' => 'Date',
                'format' => 'yyyy-MM-dd',
                'input' => 'string',
                'input_format' => 'Y-m-d',
            ])
            ->add('timeSpent', NumberType::class, [
                'label' => 'Spent time in hours',
                'scale' => 2,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $task->getId() === null ? 'Add task' : 'Update task',
            ]);
    }
}
