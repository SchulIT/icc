<?php

namespace App\Form;

use App\Entity\MessageFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFileType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('label', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.messages_files.file_label'
                ]
            ])
            ->add('extension', TextType::class, [
                'attr' => [
                    'placeholder' => 'label.messages_files.file_extension'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', MessageFile::class);
    }

    public function getBlockPrefix(): string {
        return 'message_file';
    }
}