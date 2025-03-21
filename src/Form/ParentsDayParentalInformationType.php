<?php

namespace App\Form;

use App\Entity\ParentsDayParentalInformation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentsDayParentalInformationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        parent::buildForm($builder, $options);

        $builder
            ->add('isAppointmentNotNecessary', CheckboxType::class, [
                'required' => false,
                'label' => 'parents_day.prepare.not_necessary.label',
                'attr' => [
                    'data-check-all' => 'not_necessary',
                ]
            ])
            ->add('isAppointmentRequested', CheckboxType::class, [
                'required' => false,
                'label' => 'parents_day.prepare.requested.label'
            ])
            ->add('comment', TextType::class, [
                'required' => false,
                'label' => 'label.comment',
                'attr' => [
                    'placeholder' => 'label.comment'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ParentsDayParentalInformation::class);
    }
}