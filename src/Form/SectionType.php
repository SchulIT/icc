<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SectionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('year', IntegerType::class, [
                'label' => 'label.year'
            ])
            ->add('number', IntegerType::class, [
                'label' => 'label.number'
            ])
            ->add('displayName', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('start', DateType::class, [
                'label' => 'label.start',
                'widget' => 'single_text'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end',
                'widget' => 'single_text'
            ]);
    }
}