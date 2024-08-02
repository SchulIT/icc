<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WeekOverrideType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('week', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.untis_week.help'
                ]
            ])
            ->add('overrides', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.override_weeks.help'
                ]
            ]);
    }
}