<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SubjectType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id',
                'required' => false
            ])
            ->add('abbreviation', TextType::class, [
                'label' => 'label.abbreviation',
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('replaceSubjectAbbreviation', CheckboxType::class, [
                'label' => 'admin.subjects.replace.long',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'required' => false
            ])
            ->add('color', ColorType::class, [
                'label' => 'label.color',
                'required' => false
            ])
            ->add('isVisibleGrades', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.grades',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isVisibleStudents', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.students',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isVisibleTeachers', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.teachers',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isVisibleRooms', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.rooms',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isVisibleSubjects', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.subjects',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('isVisibleLists', CheckboxType::class, [
                'label' => 'admin.subjects.visibility.lists',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}