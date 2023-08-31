<?php

namespace App\Form;

use App\Entity\Room;
use App\Sorting\RoomNameStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExamType extends AbstractType {

    public function __construct(private readonly RoomNameStrategy $roomStrategy, private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('date', DateType::class, [
                            'label' => 'label.date',
                            'widget' => 'single_text'
                        ])
                        ->add('lessonStart', IntegerType::class, [
                            'label' => 'label.start'
                        ])
                        ->add('lessonEnd', IntegerType::class, [
                            'label' => 'label.end'
                        ])
                        ->add('room', SortableEntityType::class, [
                            'label' => 'label.room',
                            'attr' => [
                                'size' => 10,
                                'data-choice' => 'true'
                            ],
                            'class' => Room::class,
                            'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('r')
                                ->where('r.isReservationEnabled = true'),
                            'choice_label' => fn(Room $room) => $room->getName(),
                            'sort_by' => $this->roomStrategy,
                            'required' => false,
                            'placeholder' => 'label.select.room',
                            'help' => 'label.room_reservation_hint'
                        ])
                        ->add('description', TextareaType::class, [
                            'label' => 'label.description',
                            'required' => false
                        ]);

                    if($this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR')) {
                        $builder->add('tuitionTeachersCanEditExam', CheckboxType::class, [
                            'label' => 'admin.exams.tuition_teachers_can_edit.label',
                            'help' => 'admin.exams.tuition_teachers_can_edit.help',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                    }
                }
            ])
            ->add('group_tuitions', FieldsetType::class, [
                'legend' => 'label.tuitions',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('tuitions', TuitionChoiceType::class, [
                            'attr' => [
                                'size' => 10,
                                'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                            ],
                            'label' => 'label.tuitions',
                            'multiple' => true,
                            'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                        ]);

                    $builder
                        ->add('addStudents', CheckboxType::class, [
                            'required' => false,
                            'mapped' => false,
                            'label' => 'admin.exams.students.add_all',
                            'help' => 'admin.exams.students.info',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ]);
    }
}