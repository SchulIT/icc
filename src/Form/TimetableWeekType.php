<?php

namespace App\Form;

use App\Entity\Week;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableWeekType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('key', TextType::class, [
                'label' => 'label.key',
            ])
            ->add('displayName', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('weeks', WeekType::class, [
                'label' => 'label.weeks',
                'class' => Week::class,
                'multiple' => true
            ]);
    }
}