<?php

namespace App\Form;

use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Subject;
use App\Entity\Teacher;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryCreateType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('csrf_field_name', '_token');
        $resolver->setDefault('csrf_token_id', 'lesson_entry_create');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end'
            ])
            ->add('topic', TextType::class, [
                'label' => 'label.topic'
            ])
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
                'label' => 'label.teacher',
                'disabled' => true,
                'choice_value' => function(?Teacher $teacher) {
                    return $teacher?->getUuid()->toString();

                }
            ])
            ->add('replacementTeacher', EntityType::class, [
                'class' => Teacher::class,
                'label' => 'label.replacement_teacher',
                'required' => false,
                'placeholder' => 'label.select.teacher',
                'choice_value' => function(?Teacher $teacher) {
                    return $teacher?->getUuid()->toString();

                }
            ])
            ->add('replacementSubject', TextType::class, [
                'label' => 'label.replacement_subject',
                'required' => false
            ])
            ->add('exercises', TextareaType::class, [
                'label' => 'label.exercises',
                'required' => false
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'book.entry.comment.label',
                'help' => 'book.entry.comment.help',
                'required' => false
            ])
            ->add('attendances', CollectionType::class, [
                'entry_type' => AttendanceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                $entry = $event->getData();

                if($entry instanceof LessonEntry) {
                    $teachers = array_map(fn(Teacher $teacher) => $teacher->getId(), $entry->getTuition()->getTeachers()->toArray());

                    if($entry->getReplacementTeacher() !== null && in_array($entry->getReplacementTeacher()->getId(), $teachers)) {
                        $entry->setReplacementTeacher(null);
                    }
                }
            });
    }
}