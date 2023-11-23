<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StudentAbsenceTypeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('details', TextType::class, [
                'label' => 'label.details',
                'required' => false
            ])
            ->add('bookLabel', TextType::class, [
                'label' => 'label.book_label.label',
                'help' => 'label.book_label.help'
            ])
            ->add('mustApprove', CheckboxType::class, [
                'label' => 'label.must_approve.label',
                'help' => 'label.must_approve.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isTypeWithZeroAbsenceLessons', CheckboxType::class, [
                'label' => 'label.type_with_zero_absence_lesson.label',
                'help' => 'label.type_with_zero_absence_lesson.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isAlwaysExcused', CheckboxType::class, [
                'label' => 'label.always_excused.label',
                'help' => 'label.always_excused.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('allowedUserTypes', UserTypeEntityType::class, [
                'label' => 'label.usertypes',
                'help' => 'label.allowed.absent_type_help',
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('additionalRecipients', CollectionType::class, [
                'entry_type' => EmailCollectionEntryType::class,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }
}