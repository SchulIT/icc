<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TeacherTagType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('color', ColorType::class, [
                'label' => 'label.color'
            ])
            ->add('visibilities', UserTypeEntityType::class, [
                'label' => 'label.visibility',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}