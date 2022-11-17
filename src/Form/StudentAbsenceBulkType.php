<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceType as StudentAbsenceTypeEntity;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\StudentRepositoryInterface;
use App\Security\Voter\StudentAbsenceTypeVoter;
use App\Security\Voter\StudentAbsenceVoter;
use App\Sorting\StudentStrategy;
use App\Utils\EnumArrayUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsenceBulkType extends AbstractType {

    public function __construct(private StudentStringConverter $studentConverter, private TranslatorInterface $translator,
                                private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('students', StudentsType::class, [
                'choice_label' => fn(Student $student) => $this->studentConverter->convert($student, true),
                'placeholder' => 'label.select.students',
                'multiple' => true,
                'apply_from_studygroups' => true,
                'label' => 'label.students_simple'
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