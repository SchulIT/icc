<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class SubstitutionHtmlImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('suppressNotifications', CheckboxType::class, [
            'label' => 'label.suppress_notifications',
            'required' => false,
            'label_attr' => [
                'class' => 'checkbox-custom'
            ]
        ])
            ->add('importFiles', FileType::class, [
                'label' => 'HTML-Dateien',
                'multiple' => true
            ]);
    }
}