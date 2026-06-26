<?php

namespace App\Appointment\Import\OpenHolidaysApi;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.end'
            ]);
    }
}
