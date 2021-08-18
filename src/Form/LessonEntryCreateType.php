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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryCreateType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('csrf_field_name', '_token');
        $resolver->setDefault('csrf_token_id', 'lesson_entry_create');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
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
                    if($teacher === null) {
                        return null;
                    }

                    return $teacher->getUuid()->toString();
                }
            ])
            ->add('replacementTeacher', EntityType::class, [
                'class' => Teacher::class,
                'label' => 'label.replacement_teacher',
                'required' => false,
                'placeholder' => 'label.select.teacher',
                'choice_value' => function(?Teacher $teacher) {
                    if($teacher === null) {
                        return null;
                    }

                    return $teacher->getUuid()->toString();
                }
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'label' => 'label.subject',
                'disabled' => true
            ])
            ->add('replacementSubject', TextType::class, [
                'label' => 'label.replacement_subject',
                'required' => false
            ])
            ->add('exercises', MarkdownType::class, [
                'label' => 'label.exercises',
                'required' => false
            ])
            ->add('comment', MarkdownType::class, [
                'label' => 'label.comment',
                'required' => false
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $entry = $event->getData();

                if($entry !== null && $entry instanceof LessonEntry) {
                    $form->add('absentStudents', StudentsType::class, [
                        'label' => 'label.absent_students',
                        'required' => false,
                        'multiple' => true,
                        'mapped' => false,
                        'choice_value' => function(Student $student) {
                            return $student->getUuid()->toString();
                        },
                        'query_builder' => function(EntityRepository $repository) use($entry) {
                            return $repository->createQueryBuilder('s')
                                ->where('s.id IN (:ids)')
                                ->setParameter(
                                    'ids',
                                    $entry->getTuition()->getStudyGroup()->getMemberships()
                                        ->map(function(StudyGroupMembership $membership) {
                                            return $membership->getStudent()->getId();
                                        }));
                        }
                    ]);
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                $entry = $event->getData();

                if($entry !== null && $entry instanceof LessonEntry) {
                    $teachers = array_map(function(Teacher $teacher) {
                        return $teacher->getId();
                    }, $entry->getTuition()->getTeachers()->toArray());

                    if($entry->getReplacementTeacher() !== null && in_array($entry->getReplacementTeacher()->getId(), $teachers)) {
                        $entry->setReplacementTeacher(null);
                    }
                }
            });
    }
}