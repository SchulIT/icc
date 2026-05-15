<?php

namespace App\TeacherAbsence\Form;

use App\Converter\TimetableLessonStringConverter;
use App\Common\Form\Type\MarkdownType;
use App\TeacherAbsence\Entity\TeacherAbsenceComment;
use App\Timetable\Entity\TimetableLesson;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherAbsenceCommentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('comment', MarkdownType::class, [
                'label' => 'absences.teachers.comment.label'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', TeacherAbsenceComment::class);
    }
}