<?php

namespace App\Form;

use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class LessonAttendanceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', StudentsType::class, [
                'label' => 'label.student',
                'required' => true,
                'constraints' => [
                    new NotNull()
                ],
                'placeholder' => 'label.select.student',
                'multiple' => false,
                'choice_value' => function(?Student $student) {
                    if($student === null) {
                        return null;
                    }

                    return $student->getUuid()->toString();
                }
            ])
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