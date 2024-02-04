<?php

namespace App\Form;

use App\Entity\Room;
use App\Settings\ExamSettings;
use App\Sorting\RoomNameStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExamType extends AbstractType {

    public function __construct(private readonly RoomNameStrategy $roomStrategy, private readonly AuthorizationCheckerInterface $authorizationChecker, private readonly ExamSettings $examSettings)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
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
            ])->add('tuitions', TuitionChoiceType::class, [
                'attr' => [
                    'size' => 10,
                    'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                ],
                'label' => 'label.tuitions',
                'multiple' => true,
                'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
            ])->add('addStudents', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'admin.exams.students.add_all',
                'help' => 'admin.exams.students.info',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'data' => true
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if($this->examSettings->isRoomReservationAllowed() !== true) {
                $event->getForm()->remove('room');
            }
        });
    }
}