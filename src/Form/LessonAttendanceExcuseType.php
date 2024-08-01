<?php

namespace App\Form;

use App\Entity\AttendanceExcuseStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonAttendanceExcuseType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);
        $resolver->setDefault('csrf_field_name', '_token');
        $resolver->setDefault('csrf_token_id', 'lesson_attendance_excuse_status');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('excuseStatus', ChoiceType::class, [
                'choices' => [
                    'book.student.not_set' => AttendanceExcuseStatus::NotSet,
                    'book.student.excused' => AttendanceExcuseStatus::Excused,
                    'book.student.not_excused' => AttendanceExcuseStatus::NotExcused
                ],
                'label' => 'label.status'
            ]);
    }
}