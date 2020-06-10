<?php

namespace App\Validator;

use App\Converter\TeacherStringConverter;
use App\Entity\RoomReservation;
use App\Rooms\Reservation\RoomAvailabilityHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoReservationCollisionValidator extends ConstraintValidator {

    private $availabilityHelper;
    private $teacherConverter;

    public function __construct(RoomAvailabilityHelper $availabilityHelper, TeacherStringConverter $teacherConverter) {
        $this->availabilityHelper = $availabilityHelper;
        $this->teacherConverter = $teacherConverter;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof NoReservationCollision) {
            throw new UnexpectedTypeException($constraint, NoReservationCollision::class);
        }

        if(!$value instanceof RoomReservation) {
            throw new UnexpectedTypeException($value, RoomReservation::class);
        }

        $room = $value->getRoom();
        $date = $value->getDate();
        $lessonStart = $value->getLessonStart();
        $lessonEnd = $value->getLessonEnd();

        if($room === null || $date === null || $lessonStart <= 0 || $lessonEnd <= 0 || $lessonStart > $lessonEnd) {
            // reservation must be filled with serious information first
            return;
        }

        for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
            $availability = $this->availabilityHelper->getAvailability($room, $date, $lessonNumber);

            if($availability === null) {
                continue;
            }

            $timetableLesson = $availability->getTimetableLesson();

            if($timetableLesson !== null && $availability->isTimetableLessonCancelled() === false) {
                $this->context
                    ->buildViolation($constraint->messageTimetable)
                    ->setParameter('{{ tuition }}', $timetableLesson->getTuition()->getName())
                    ->setParameter('{{ teacher }}', $this->teacherConverter->convert($timetableLesson->getTuition()->getTeacher()))
                    ->setParameter('{{ lessonNumber }}', (string)$lessonNumber)
                    ->addViolation();
            }

            $substitution = $availability->getSubstitution();

            if($substitution !== null) {
                $this->context
                    ->buildViolation($constraint->messageSubstitution)
                    ->setParameter('{{ lessonNumber }}', (string)$lessonNumber)
                    ->addViolation();
            }

            $existingReservation = $availability->getReservation();

            if($existingReservation !== null && $existingReservation->getId() !== $value->getId()) {
                $this->context
                    ->buildViolation($constraint->messageReservation)
                    ->setParameter('{{ teacher }}', $this->teacherConverter->convert($existingReservation->getTeacher()))
                    ->setParameter('{{ lessonNumber }}', (string)$lessonNumber)
                    ->addViolation();
            }
        }
    }
}