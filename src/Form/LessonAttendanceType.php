<?php

namespace App\Form;

use App\Entity\LessonAttendance;
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
            ->add('lateMinutes', IntegerType::class, [])
            ->add('comment', TextType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', LessonAttendance::class);
    }
}