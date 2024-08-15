<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookEventCreateType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text',
            ])
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end'
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
                'label' => 'label.teacher',
                'choice_value' => function(?Teacher $teacher) {
                    return $teacher?->getUuid()->toString();
                }
            ])
            ->add('students', StudentsType::class, [
                'label' => 'label.students_simple',
                'mapped' => false,
                'multiple' => true,
                'apply_from_studygroups' => true,
            ]);
    }
}