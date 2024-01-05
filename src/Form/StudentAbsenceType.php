<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceType as StudentAbsenceTypeEntity;
use App\Entity\User;
use App\Repository\StudentRepositoryInterface;
use App\Security\Voter\StudentAbsenceTypeVoter;
use App\Sorting\StudentStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsenceType extends AbstractType {

    private const ShowComboboxThreshold = 10;

    public function __construct(private readonly StudentStringConverter $studentConverter, private readonly StudentStrategy $studentStrategy,
                                private readonly TranslatorInterface $translator, private readonly AuthorizationCheckerInterface $authorizationChecker,
                                private readonly TokenStorageInterface $tokenStorage, private readonly StudentRepositoryInterface $studentRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return;
        }

        if($user->isStudentOrParent() || $user->getStudents()->count() > 0) {
            $students = $user->getStudents()->toArray();

            if($user->isStudent()) {
                $students = [ array_shift($students) ];
            }
        } else {
            $students = $this->studentRepository->findAll();
        }

        $builder
            ->add('student', SortableChoiceType::class, [
                'choices' => $students,
                'choice_label' => fn(Student $student) => $this->studentConverter->convert($student, true),
                'placeholder' => 'label.select.student',
                'attr' => [
                    'data-choice' => 'true',
                    'class' => 'custom-select'
                ],
                'sort_by' => $this->studentStrategy,
                'label' => 'label.student',
                'expanded' => count($students) < self::ShowComboboxThreshold
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
                },
                'choice_filter' => function(?StudentAbsenceTypeEntity $type) {
                    if($type === null) {
                        return true;
                    }

                    return $this->authorizationChecker->isGranted(StudentAbsenceTypeVoter::USE, $type);
                }
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'absences.students.add.absent_from'
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'absences.students.add.absent_until'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'absences.students.add.message',
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
                'label' => 'absences.students.add.phone'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'label.email'
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use($students, $user) {
                $form = $event->getForm();
                /** @var StudentAbsence $absence */
                $absence = $event->getData();

                if($absence->getId() !== null) {
                    $form
                        ->add('student', SortableChoiceType::class, [
                            'choices' => $students,
                            'choice_label' => fn(Student $student) => $this->studentConverter->convert($student, true),
                            'placeholder' => 'label.select.student',
                            'attr' => [
                                'data-choice' => 'true',
                                'class' => 'custom-select'
                            ],
                            'sort_by' => $this->studentStrategy,
                            'label' => 'label.student',
                            'disabled' => true
                        ])
                        ->add('message', TextareaType::class, [
                            'label' => 'absences.students.add.message',
                            'attr' => [
                                'rows' => 5
                            ],
                            'disabled' => true
                        ]);

                    if($user->isStudentOrParent()) {
                        // Students and parents are not allowed to modifiy the date of a approved type

                        if($absence->getType()->isMustApprove() && $absence->isApproved()) {
                            $form
                                ->add('from', DateLessonType::class, [
                                    'label' => 'absences.students.add.absent_from',
                                    'disabled' => true
                                ])
                                ->add('until', DateLessonType::class, [
                                    'label' => 'absences.students.add.absent_until',
                                    'disabled' => true
                                ]);
                        } elseif ($absence->getType()->isMustApprove() === false) {
                            $form
                                ->add('from', DateLessonType::class, [
                                    'label' => 'absences.students.add.absent_from',
                                    'disabled' => true
                                ]);
                        }

                        // Students and parents are not allowed to change absence type
                        $form
                            ->add('type', EntityType::class, [
                                'expanded' => true,
                                'label_attr' => [
                                    'class' => 'radio-custom'
                                ],
                                'label' => 'label.type',
                                'class' => StudentAbsenceTypeEntity::class,
                                'choice_label' => function(StudentAbsenceTypeEntity $type) {
                                    if($type->isMustApprove()) {
                                        return sprintf('%s (%s)', $type->getName(), $this->translator->trans('absences.students.add.must_approve.label'));
                                    }

                                    return $type->getName();
                                },
                                'choice_filter' => function(?StudentAbsenceTypeEntity $type) {
                                    if($type === null) {
                                        return true;
                                    }

                                    return $this->authorizationChecker->isGranted(StudentAbsenceTypeVoter::USE, $type);
                                },
                                'disabled' => true
                            ]);
                    }
                }
            });
    }
}