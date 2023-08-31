<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SubjectOverrideType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('untis', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.untis_subject'
                ]
            ])
            ->add('override', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.icc_subject'
                ]
            ]);
    }
}