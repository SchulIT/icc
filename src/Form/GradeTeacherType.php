<?php

namespace App\Form;

use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeTeacherType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('teacher', EntityType::class, [
                'label' => 'label.teacher',
                'class' => Teacher::class,
                'choice_label' => fn(Teacher $teacher) => $teacher->getAcronym()
            ])
            ->add('section', EntityType::class, [
                'label' => 'label.section',
                'class' => Section::class,
                'choice_label' => fn(Section $section) => $section->getDisplayName()
            ])
            ->add('type', EnumType::class, [
                'label' => 'label.type',
                'class' => \App\Entity\GradeTeacherType::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', GradeTeacher::class);
    }
}