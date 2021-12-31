<?php

namespace App\Validator;

use App\Converter\TeacherStringConverter;
use App\Entity\Exam;
use App\Entity\ResourceReservation;
use App\Rooms\Reservation\ResourceAvailabilityHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoReservationCollisionValidator extends ConstraintValidator {

    private ResourceAvailabilityHelper $availabilityHelper;
    private TeacherStringConverter $teacherConverter;

    public function __construct(ResourceAvailabilityHelper $availabilityHelper, TeacherStringConverter $teacherConverter) {
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

        if(!$value instanceof ResourceReservation && !$value instanceof Exam) {
            throw new UnexpectedTypeException($value, ResourceReservation::class);
        }

        $room = $value instanceof Exam ? $value->getRoom() : $value->getResource();
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
                $tuition = null;

                if($timetableLesson->getTuition() !== null) {
                    $tuition = $timetableLesson->getTuition()->getName();
                } else {
                    $tuition = $timetableLesson->getSubject();
                }

                $this->context
                    ->buildViolation($constraint->messageTimetable)
                    ->setParameter('{{ tuition }}', $tuition)
                    ->setParameter('{{ teacher }}', $this->teacherConverter->convert($timetableLesson->getTeachers()->first()))
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

            $exams = $availability->getExams();

            if(count($exams) > 0) {
                foreach($exams as $collidingExam) {
                    if($value instanceof Exam && $value->getId() === $collidingExam->getId()) {
                        continue;
                    }

                    $this->context
                        ->buildViolation($constraint->messageExam)
                        ->setParameter('{{ lessonNumber }}', (string)$lessonNumber)
                        ->addViolation();
                }
            }
        }
    }
}