<?php

namespace App\Book\Grade\Export\ZP10;

use App\Entity\Section;
use App\Entity\TuitionGradeCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigurationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'label' => 'label.section',
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('abschlussNote', EntityType::class, [
                'class' => TuitionGradeCategory::class,
                'label' => 'Abschlussnote',
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('vornote', EntityType::class, [
                'class' => TuitionGradeCategory::class,
                'label' => 'Vornote',
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('schriftlich', EntityType::class, [
                'class' => TuitionGradeCategory::class,
                'label' => 'Schriftlich',
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('muendlich', EntityType::class, [
                'class' => TuitionGradeCategory::class,
                'label' => 'Mündlich',
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);
    }
}