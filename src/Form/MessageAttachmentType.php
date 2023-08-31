<?php

namespace App\Form;

use App\Entity\MessageAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MessageAttachmentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('file', VichFileType::class, [
                'label' => 'label.file',
                'attr' => [
                    'in_collection' => true
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', MessageAttachment::class);
    }
}