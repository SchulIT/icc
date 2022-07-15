<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Entity\StudentAbsenceType as StudentAbsenceTypeEntity;
use App\Settings\StudentAbsenceSettings;
use App\Sorting\StudentStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentAbsenceType extends AbstractType {

    private StudentStringConverter $studentConverter;
    private StudentStrategy $studentStrategy;

    public function __construct(StudentStringConverter $converter, StudentStrategy $strategy, StudentAbsenceSettings $settings) {
        $this->studentConverter = $converter;
        $this->studentStrategy = $strategy;
        $this->settings = $settings;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('students');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', SortableChoiceType::class, [
                'choices' => $options['students'],
                'choice_label' => function(Student $student) {
                    return $this->studentConverter->convert($student, true);
                },
                'placeholder' => 'label.select.student',
                'attr' => [
                    'data-choice' => 'true',
                    'class' => 'custom-select'
                ],
                'sort_by' => $this->studentStrategy,
                'label' => 'label.student'
            ])
            ->add('type', EntityType::class, [
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'label' => 'label.type',
                'class' => StudentAbsenceTypeEntity::class,
                'choice_label' => function(StudentAbsenceTypeEntity $type) {
                    return $type->getName();
                }
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'student_absences.add.absent_from'
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'student_absences.add.absent_until'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'student_absences.add.message',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => StudentAbsenceAttachmentType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'student_absences.add.phone'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'label.email'
            ]);
    }
}