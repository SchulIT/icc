<?php

namespace App\Form;

use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceExcuseStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonAttendanceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('type', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'attendance.present' => \App\Entity\LessonAttendanceType::Present,
                    'attendance.delayed' => \App\Entity\LessonAttendanceType::Late,
                    'attendance.absent' => \App\Entity\LessonAttendanceType::Absent
                ]
            ])
            ->add('excuseStatus', ChoiceType::class, [
                'choices' => [
                    'book.student.not_set' => LessonAttendanceExcuseStatus::NotSet,
                    'book.student.excused' => LessonAttendanceExcuseStatus::Excused,
                    'book.student.not_excused' => LessonAttendanceExcuseStatus::NotExcused
                ],
                'label' => 'label.status'
            ])
            ->add('lateMinutes', IntegerType::class, [])
            ->add('absentLessons', IntegerType::class, [])
            ->add('comment', TextType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', LessonAttendance::class);
    }
}