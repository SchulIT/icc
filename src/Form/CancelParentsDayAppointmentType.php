<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CancelParentsDayAppointmentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('confirm', CheckboxType::class, [
                'label' => 'parents_day.appointments.cancel_all.confirm',
                'constraints' => [
                    new IsTrue()
                ]
            ])
            ->add('reason', TextType::class, [
                'label' => 'label.reason',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 150)
                ]
            ]);
    }
}