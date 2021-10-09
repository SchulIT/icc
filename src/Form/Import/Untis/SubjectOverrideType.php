<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SubjectOverrideType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('untis', TextType::class, [
                'label' => 'label.untis_subject'
            ])
            ->add('override', TextType::class, [
                'label' => 'label.subject'
            ]);
    }
}