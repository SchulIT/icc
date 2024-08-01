<?php

namespace App\Form;

use App\Entity\Attendance;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceFlag;
use App\Entity\AttendanceType;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class LessonAttendanceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('lesson', IntegerType::class, [
                'required' => true,
            ])
            ->add('student', StudentsType::class, [
                'label' => 'label.student',
                'required' => true,
                'constraints' => [
                    new NotNull()
                ],
                'placeholder' => 'label.select.student',
                'multiple' => false,
                'choice_value' => function(?Student $student) {
                    return $student?->getUuid()->toString();
                }
            ])
            ->add('type', EnumType::class, [
                'class' => AttendanceType::class,
                'expanded' => true
            ])
            ->add('excuseStatus', EnumType::class, [
                'class' => AttendanceExcuseStatus::class,
                'label' => 'label.status',
                'expanded' => true
            ])
            ->add('flags', EntityType::class, [
                'class' => AttendanceFlag::class,
                'multiple' => true,
                'choice_label' => fn(AttendanceFlag $flag) => $flag->getDescription(),
                'required' => false,
                'expanded' => true
            ])
            ->add('lateMinutes', IntegerType::class, [])
            ->add('isZeroAbsentLesson', CheckboxType::class, [
                'required' => false,
                'false_values' => [ 0, '0']
            ])
            ->add('comment', TextType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', Attendance::class);
    }
}