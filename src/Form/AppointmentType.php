<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\Teacher;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\TeacherStrategy;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentType extends AbstractType {
    private $teacherConverter;
    private $teacherStrategy;
    private $appointmentCategoryStrategy;

    public function __construct(TeacherStringConverter $teacherConverter, TeacherStrategy $teacherStrategy, AppointmentCategoryStrategy $appointmentCategoryStrategy) {
        $this->teacherConverter = $teacherConverter;
        $this->teacherStrategy = $teacherStrategy;
        $this->appointmentCategoryStrategy = $appointmentCategoryStrategy;
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
                            'choice_label' => function(AppointmentCategory $appointmentCategory) {
                                return $appointmentCategory->getName();
                            },
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
                            'time_widget' => 'single_text'
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
                            'choice_label' => function(Teacher $teacher) {
                                return $this->teacherConverter->convert($teacher);
                            },
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