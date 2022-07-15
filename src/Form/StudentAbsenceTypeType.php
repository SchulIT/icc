<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StudentAbsenceTypeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('mustApprove', CheckboxType::class, [
                'label' => 'label.must_approve.label',
                'help' => 'label.must_approve.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}