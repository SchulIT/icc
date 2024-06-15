<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamSplitConfigurationType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefined('exam_id');
        $resolver->setRequired('exam_id');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('splits', CollectionType::class, [
                'entry_type' => ExamSplitType::class,
                'allow_add' => true,
                'by_reference' => false,
                'entry_options' => [
                    'exam_id' => $options['exam_id']
                ]
            ]);
    }
}