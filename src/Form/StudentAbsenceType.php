<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Entity\StudentAbsenceType as StudentAbsenceTypeEntity;
use App\Security\Voter\StudentAbsenceTypeVoter;
use App\Security\Voter\StudentAbsenceVoter;
use App\Sorting\StudentStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsenceType extends AbstractType {

    public function __construct(private StudentStringConverter $studentConverter, private StudentStrategy $studentStrategy, private TranslatorInterface $translator, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('students');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', SortableChoiceType::class, [
                'choices' => $options['students'],
                'choice_label' => fn(Student $student) => $this->studentConverter->convert($student, true),
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
                    if($type->isMustApprove()) {
                        return sprintf('%s (%s)', $type->getName(), $this->translator->trans('student_absences.add.must_approve.label'));
                    }

                    return $type->getName();
                },
                'choice_filter' => function(?StudentAbsenceTypeEntity $type) {
                    if($type === null) {
                        return true;
                    }

                    return $this->authorizationChecker->isGranted(StudentAbsenceTypeVoter::USE, $type);
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