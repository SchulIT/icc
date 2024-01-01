<?php

namespace App\Form;

use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceFlag;
use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('bookAttendanceType', ChoiceType::class, [
                'choices' => [
                    'book.attendance.type.present' => \App\Entity\LessonAttendanceType::Present,
                    'book.attendance.type.absent' => \App\Entity\LessonAttendanceType::Absent
                ],
                'label' => 'label.book_attendance_type.label',
                'help' => 'label.book_attendance_type.help',
                'expanded' => true
            ])
            ->add('bookExcuseStatus', ChoiceType::class, [
                'choices' => [
                    'book.students.excused' => LessonAttendanceExcuseStatus::Excused,
                    'book.students.not_excused' => LessonAttendanceExcuseStatus::NotExcused,
                    'book.students.not_set' => LessonAttendanceExcuseStatus::NotSet
                ],
                'label' => 'label.book_excuse_status.label',
                'help' => 'label.book_excuse_status.help',
                'expanded' => true
            ])
            ->add('subjects', EntityType::class, [
                'label' => 'label.book_subjects.label',
                'help' => 'label.book_subjects.help',
                'required' => false,
                'class' => Subject::class,
                'multiple' => true,
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('s')->orderBy('s.name'),
                'choice_label' => fn(Subject $subject) => $subject->getName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('notifySubjectTeacher', CheckboxType::class, [
                'required' => false,
                'label' => 'label.book_subjects.notify.label',
                'help' => 'label.book_subjects.notify.help'
            ])
            ->add('flags', EntityType::class, [
                'label' => 'label.book_flags.label',
                'help' => 'label.book_flags.help',
                'required' => false,
                'class' => LessonAttendanceFlag::class,
                'multiple' => true,
                'choice_label' => fn(LessonAttendanceFlag $flag) => $flag->getDescription(),
                'attr' => [
                    'data-choice' => 'true'
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