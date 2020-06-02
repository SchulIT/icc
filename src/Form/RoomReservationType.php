<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\Room;
use App\Entity\Teacher;
use App\Sorting\RoomNameStrategy;
use App\Sorting\TeacherStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomReservationType extends AbstractType {

    private $roomStratgy;
    private $teacherStrategy;
    private $teacherConverter;

    public function __construct(RoomNameStrategy $roomStratgy, TeacherStrategy $teacherStrategy, TeacherStringConverter $teacherConverter) {
        $this->roomStratgy = $roomStratgy;
        $this->teacherStrategy = $teacherStrategy;
        $this->teacherConverter = $teacherConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('room', SortableEntityType::class, [
                'label' => 'label.room',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => Room::class,
                'choice_label' => function(Room $room) {
                    return $room->getName();
                },
                'sort_by' => $this->roomStratgy
            ])
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
            ->add('teacher', SortableEntityType::class, [
                'label' => 'label.teacher',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => Teacher::class,
                'choice_label' => function(Teacher $teacher) {
                    return $this->teacherConverter->convert($teacher);
                },
                'sort_by' => $this->teacherStrategy,

            ]);
    }
}