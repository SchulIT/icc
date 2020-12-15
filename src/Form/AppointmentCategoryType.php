<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentCategoryType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id',
                'required' => false
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('color', ColorType::class, [
                'label' => 'label.color'
            ])
            ->add('usersCanCreateAppointments', CheckboxType::class, [
                'label' => 'label.users_can_create_appointments.label',
                'help' => 'label.users_can_create_appointments.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}