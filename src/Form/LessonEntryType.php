<?php

namespace App\Form;

use App\Entity\LessonEntry;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start',
                /*'attr' => [
                    'min' => $options['lesson_start'],
                    'max' => $options['lesson_end']
                ]*/
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end',
                /*'attr' => [
                    'min' => $options['lesson_start'],
                    'max' => $options['lesson_end']
                ]*/
            ])
            ->add('subject', TextType::class, [
                'label' => 'label.subject',
                'required' => false,
                'disabled' => true
            ])
            ->add('replacementSubject', TextType::class, [
                'label' => 'label.replacement_subject',
                'required' => false
            ])
            ->add('teacher', TeacherChoiceType::class, [
                'required' => false,
                'disabled' => true
            ])
            ->add('replacementTeacher', TeacherChoiceType::class, [
                'label' => 'label.replacement_teacher',
                'required' => false,
                'placeholder' => 'label.select.teacher'
            ])
            ->add('topic', TextType::class, [
                'label' => 'label.topic'
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'label.comment'
            ])
            ->add('attendances', CollectionType::class, [
                'entry_type' => LessonAttendanceType::class
            ]);

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $entry = $event->getData();

                if($entry !== null && $entry instanceof LessonEntry) {
                    if($entry->isCancelled()) {
                        $form->remove('replacementSubject')
                            ->remove('replacementTeacher')
                            ->remove('topic')
                            ->remove('comment')
                            ->remove('attendances');

                        $form->add('cancelReason', TextType::class, [
                            'label' => 'book.entry.cancel.reason',
                            'required' => true
                        ]);
                    }
                }
            });
    }
}