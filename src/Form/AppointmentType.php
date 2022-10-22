<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\Teacher;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\TeacherStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AppointmentType extends AbstractType {
    public function __construct(private TeacherStringConverter $teacherConverter, private TeacherStrategy $teacherStrategy, private AppointmentCategoryStrategy $appointmentCategoryStrategy, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' =>  'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('externalId', TextType::class, [
                            'disabled' => true,
                            'required' => false,
                            'label' => 'label.external_id'
                        ])
                        ->add('title', TextType::class, [
                            'label' => 'label.title'
                        ])
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.content'
                        ])
                        ->add('category', SortableEntityType::class, [
                            'label' => 'label.category',
                            'required' => true,
                            'class' => AppointmentCategory::class,
                            'sort_by' => $this->appointmentCategoryStrategy,
                            'query_builder' => function(EntityRepository $repository) {
                                $qb = $repository->createQueryBuilder('c');

                                if($this->authorizationChecker->isGranted('ROLE_APPOINTMENTS_ADMIN') !== true) {
                                    $qb->where('c.usersCanCreateAppointments = true');
                                }

                                return $qb;
                            },
                            'choice_label' => fn(AppointmentCategory $appointmentCategory) => $appointmentCategory->getName(),
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('start', DateTimeType::class, [
                            'label' => 'label.start',
                            'date_widget' => 'single_text',
                            'time_widget' => 'single_text'
                        ])
                        ->add('end', DateTimeType::class, [
                            'label' => 'label.end',
                            'date_widget' => 'single_text',
                            'time_widget' => 'single_text',
                            'help' => 'label.end_hint'
                        ])
                        ->add('location', TextType::class, [
                            'label' => 'label.location',
                            'required' => false
                        ])
                        ->add('allDay', CheckboxType::class, [
                            'label' => 'label.all_day',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('markStudentsAbsent', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.mark_students_absent',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('visibilities', UserTypeEntityType::class, [
                            'label' => 'label.visibility',
                            'multiple' => true,
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ])
            ->add('group_people', FieldsetType::class, [
                'legend' => 'label.people',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('studyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ],
                            'required' => false
                        ])
                        ->add('organizers', SortableEntityType::class, [
                            'label' => 'label.organizers',
                            'required' => false,
                            'class' => Teacher::class,
                            'multiple' => true,
                            'choice_label' => fn(Teacher $teacher) => $this->teacherConverter->convert($teacher),
                            'sort_by' => $this->teacherStrategy,
                            'attr' => [
                                'size' => 10
                            ]
                        ])
                        ->add('externalOrganizers', TextType::class, [
                            'label' => 'label.external_organizers',
                            'required' => false
                        ]);
                }
            ]);
    }
}