<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryCancelType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('validation_groups', [ 'cancel']);
        $resolver->setDefault('csrf_field_name', '_token');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end'
            ])
            ->add('cancelReason', TextType::class, [
                'label' => 'book.entry.cancel.reason',
                'required' => true
            ])
            ->add('exercises', TextareaType::class, [
                'label' => 'label.exercises',
                'required' => false
            ]);
    }
}