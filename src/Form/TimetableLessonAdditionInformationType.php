<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableLessonAdditionInformationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('commentTeacher', MarkdownType::class, [
                'label' => 'absences.teachers.comment.teacher'
            ])
            ->add('commentStudents', MarkdownType::class, [
                'label' => 'absences.teachers.comment.students'
            ]);
    }
}