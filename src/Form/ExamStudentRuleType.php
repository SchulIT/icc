<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ExamStudentRuleType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('grades', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.grades'
                ]
            ])
            ->add('sections', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.sections'
                ]
            ])
            ->add('types', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.course_types'
                ]
            ]);

    }
}