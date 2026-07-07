<?php

namespace App\Common\Form;

use App\Common\Form\Type\ColorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ])
            ->add('chairs', CollectionType::class, [
                'entry_type' => SubjectChairType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'hide_teacher' => false,
                    'hide_subject' => true
                ]
            ]);
    }
}
