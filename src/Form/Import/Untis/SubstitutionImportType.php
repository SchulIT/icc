<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class SubstitutionImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.end'
            ])
            ->add('suppressNotifications', CheckboxType::class, [
                'label' => 'label.suppress_notifications',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('importFile', FileType::class, [
                'label' => 'GPU014.txt'
            ]);
    }
}