<?php

namespace App\Form;

use App\Entity\ChatMessageAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ChatMessageAttachmentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('file', VichFileType::class, [
                'label' => 'label.file',
                'download_uri' => false,
                'attr' => [
                    'in_collection' => true
                ],
                'error_bubbling' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', ChatMessageAttachment::class);
        $resolver->setDefault('error_bubbling', false);
    }
}