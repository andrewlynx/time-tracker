<?php

namespace App\Form;

use App\Entity\Task;
use App\Form\Extensions\DatePickerType;
use App\Service\Export\FileExportFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DownloadFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_from', DatePickerType::class, [
                'label' => 'Start Date',
                'format' => 'yyyy-MM-dd',
                'input' => 'string',
                'input_format' => 'Y-m-d',
            ])
            ->add('date_to', DatePickerType::class, [
                'label' => 'End Date',
                'format' => 'yyyy-MM-dd',
                'input' => 'string',
                'input_format' => 'Y-m-d',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => FileExportFactory::FORMATS,
                'choice_label' => static function (string $choice): string {
                    return $choice;
                },
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Download',
            ])
            ->setAction($options['data']['action']);
    }
}
