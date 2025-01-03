<?php

namespace App\Export\Untis\Timetable;

use App\Entity\TimetableWeek;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeekType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('week', EntityType::class, [
                'class' => TimetableWeek::class,
                'choice_label' => 'displayName'
            ])
            ->add('untisWeek', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'label.untis_week.label'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', Week::class);
    }

    public function getBlockPrefix() {
        return 'week_type';
    }
}