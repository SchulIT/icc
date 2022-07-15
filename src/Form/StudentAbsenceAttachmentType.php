<?php

namespace App\Form;

use App\Entity\StudentAbsenceAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class StudentAbsenceAttachmentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('file', VichFileType::class, [
                'label' => 'label.file',
                'attr' => [
                    'in_collection' => true
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', StudentAbsenceAttachment::class);
    }
}