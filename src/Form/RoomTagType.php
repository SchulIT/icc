<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomTagType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextType::class, [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('hasValue', CheckboxType::class, [
                'label' => 'label.has_value',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}