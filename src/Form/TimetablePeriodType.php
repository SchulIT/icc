<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetablePeriodType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('start', DateType::class, [
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end'
            ])
            ->add('visibilities', UserTypeEntityType::class, [
                'label' => 'label.visibility',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ]);
    }
}