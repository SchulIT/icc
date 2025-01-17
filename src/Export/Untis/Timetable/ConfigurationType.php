<?php

namespace App\Export\Untis\Timetable;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigurationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.end'
            ])
            ->add('weeks', CollectionType::class, [
                'entry_type' => WeekType::class,
                'allow_add' => false,
                'allow_delete' => false
            ]);
    }
}