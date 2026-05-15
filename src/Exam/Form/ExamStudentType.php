<?php

namespace App\Exam\Form;

use App\Exam\Entity\ExamStudent;
use App\Common\Form\Choice\StudentsType;
use App\Common\Form\Choice\TuitionChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamStudentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('student', StudentsType::class, [
                'multiple' => false,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'placeholder' => 'label.select.student'
            ])
            ->add('tuition', TuitionChoiceType::class, [
                'placeholder' => 'label.select.tuition',
                'attr' => [
                    'data-choice' => 'true'
                ],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', ExamStudent::class);
    }
}