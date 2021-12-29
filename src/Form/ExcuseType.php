<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ExcuseType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', StudentsType::class, [
                'label' => 'label.student',
                'placeholder' => 'label.select.student',
                'multiple' => false,
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'label.start'
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'label.end'
            ])
            ->add('excusedBy', TeacherChoiceType::class, [
                'label' => 'label.excused_by',
                'multiple' => false,
                'placeholder' => 'label.select.teacher'
            ])
            ->add('comment', MarkdownType::class, [
                'label' => 'label.comment'
            ]);
    }
}