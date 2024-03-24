<?php

namespace App\Form;

use App\Controller\MarkdownController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class BookStudentInformationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('student', StudentsType::class, [
                'multiple' => false,
                'label' => 'label.student'
            ])
            ->add('content', MarkdownType::class, [
                'label' => 'label.content'
            ])
            ->add('from', DateType::class, [
                'label' => 'label.from',
                'widget' => 'single_text'
            ])
            ->add('until', DateType::class, [
                'label' => 'label.until',
                'widget' => 'single_text'
            ])
            ->add('includeInGradeBookExport', CheckboxType::class, [
                'required' => false,
                'label' => 'label.include_in_gradebook_export.label',
                'help' => 'label.include_in_gradebook_export.help'
            ]);
    }
}