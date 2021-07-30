<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class BookCommentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text'
            ])
            ->add('teacher', TeacherChoiceType::class, [
                'label' => 'label.teacher',
                'placeholder' => 'label.select.teacher'
            ])
            ->add('text', TextareaType::class, [
                'label' => 'label.comment'
            ])
            ->add('students', StudentsType::class, [
                'label' => 'label.students_simple',
                'multiple' => true,
            ]);
    }
}