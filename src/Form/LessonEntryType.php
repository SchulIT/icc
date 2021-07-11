<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('lesson_start')
            ->setRequired('lesson_end');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.lesson_start',
                /*'attr' => [
                    'min' => $options['lesson_start'],
                    'max' => $options['lesson_end']
                ]*/
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.lesson_end',
                /*'attr' => [
                    'min' => $options['lesson_start'],
                    'max' => $options['lesson_end']
                ]*/
            ])
            ->add('isSubstitution', CheckboxType::class, [
                'label' => 'label.substitution',
                'required' => false,
                'mapped' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'label.subject',
                'required' => false,
            ])
            ->add('teacher', TeacherType::class, [
                'required' => false
            ])
            ->add('topic', TextType::class, [
                'label' => 'label.topic'
            ])
            ->add('exercises', MarkdownType::class, [
                'label' => 'label.exercises'
            ])
            ->add('attendances', CollectionType::class, [
                'entry_type' => LessonAttendanceType::class
            ]);
    }
}