<?php

namespace App\Form;

use App\Entity\ParentsDay;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentsCreatorParamsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('duration', IntegerType::class, [
                'label' => 'label.duration.label',
                'help' => 'label.duration.help_minutes'
            ])
            ->add('from', TimeType::class, [
                'label' => 'label.from',
                'widget' => 'single_text',
                'input' => 'string'
            ])
            ->add('until', TimeType::class, [
                'label' => 'label.until',
                'widget' => 'single_text',
                'input' => 'string'
            ])
            ->add('removeExistingAppointments', CheckboxType::class, [
                'required' => false,
                'label' => 'label.remove_existing_appointments.label',
                'help' => 'label.remove_existing_appointments.help'
            ]);
    }
}