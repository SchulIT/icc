<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ChatTagType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('color', ColorType::class, [
                'label' => 'label.color'
            ])
            ->add('userTypes', UserTypeEntityType::class, [
                'label' => 'label.usertypes',
                'multiple' => true,
                'expanded' => true,
            ]);
    }
}