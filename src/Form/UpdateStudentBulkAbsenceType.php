<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\StudentAbsenceType as StudentAbsenceTypeEntity;
use App\Security\Voter\StudentAbsenceTypeVoter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateStudentBulkAbsenceType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator,
                                private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('type', EntityType::class, [
                'required' => false,
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
                }
            ])
            ->add('from', DateLessonType::class, [
                'required' => false,
                'label' => 'absences.students.add.absent_from'
            ])
            ->add('until', DateLessonType::class, [
                'required' => false,
                'label' => 'absences.students.add.absent_until'
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'label' => 'absences.students.add.message',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'absences.students.add.phone'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'label.email'
            ]);
    }
}