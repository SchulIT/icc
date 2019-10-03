<?php

namespace App\Form;

use App\Entity\DocumentAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentAttachmentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('file', VichFileType::class, [
                'label' => 'label.file',
                'download_uri' => false,
                'attr' => [
                    'in_collection' => true
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', DocumentAttachment::class);
    }
}