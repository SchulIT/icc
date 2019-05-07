<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableWeekType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('key', TextType::class, [
                'label' => 'label.key',
            ])
            ->add('displayName', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('weekMod', IntegerType::class, [
                'label' => 'label.week_mod.label',
                'help' => 'label.week_mod.help'
            ]);
    }
}