<?php

namespace App\Form;

use App\Entity\LessonAttendanceExcuseStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonAttendanceExcuseType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        $resolver->setDefault('csrf_field_name', '_token');
        $resolver->setDefault('csrf_token_id', 'lesson_attendance_excuse_status');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('excuseStatus', ChoiceType::class, [
                'choices' => [
                    'book.student.not_set' => LessonAttendanceExcuseStatus::NotSet,
                    'book.student.excused' => LessonAttendanceExcuseStatus::Excused,
                    'book.student.not_excused' => LessonAttendanceExcuseStatus::NotExcused
                ],
                'label' => 'label.status'
            ]);
    }
}