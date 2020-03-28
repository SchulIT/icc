<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageUploadType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('uploads', CollectionType::class, [
                'entry_type' => MessageFileUploadType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => false
            ]);
    }
}