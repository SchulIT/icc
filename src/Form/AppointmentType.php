<?php

namespace App\Form;

use App\Converter\StudyGroupStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\TeacherStrategy;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentType extends AbstractType {
    private $studyGroupConverter;
    private $teacherConverter;
    private $stringStrategy;
    private $studyGroupStrategy;
    private $teacherStrategy;
    private $appointmentCategoryStrategy;

    public function __construct(StudyGroupStringConverter $studyGroupConverter, StringStrategy $stringStrategy, StudyGroupStrategy $studyGroupStrategy,
                                TeacherStringConverter $teacherConverter, TeacherStrategy $teacherStrategy, AppointmentCategoryStrategy $appointmentCategoryStrategy) {
        $this->studyGroupConverter = $studyGroupConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studyGroupStrategy = $studyGroupStrategy;
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
                            }
                        ])
                        ->add('start', DateTimeType::class, [
                            'label' => 'label.start'
                        ])
                        ->add('end', DateTimeType::class, [
                            'label' => 'label.end'
                        ])
                        ->add('location', TextType::class, [
                            'label' => 'label.location',
                            'required' => false
                        ])
                        ->add('allDay', CheckboxType::class, [
                            'label' => 'label.all_day',
                            'required' => false
                        ])
                        ->add('isHiddenFromStudents', CheckboxType::class, [
                            'label' => 'label.hidden_from_students',
                            'required' => false
                        ]);
                }
            ])
            ->add('group_people', FieldsetType::class, [
                'legend' => 'label.people',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('studyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups',
                            'class' => StudyGroup::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('sg')
                                    ->select(['sg', 'g'])
                                    ->orderBy('sg.name', 'asc')
                                    ->leftJoin('sg.grades', 'g');
                            },
                            'group_by' => function(StudyGroup $group) {
                                $grades = array_map(function(Grade $grade) {
                                    return $grade->getName();
                                }, $group->getGrades()->toArray());

                                return join(', ', $grades);
                            },
                            'multiple' => true,
                            'choice_label' => function(StudyGroup $group) {
                                return $this->studyGroupConverter->convert($group);
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->studyGroupStrategy,
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