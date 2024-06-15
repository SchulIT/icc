<?php

namespace App\Validator;

use App\Converter\StudentStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\ParentsDayAppointment;
use App\Repository\ParentsDayAppointmentRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoParentsDayAppointmentCollisionValidator extends ConstraintValidator {

    public function __construct(private readonly ParentsDayAppointmentRepositoryInterface $appointmentRepository,
                                private readonly StudentStringConverter $studentStringConverter,
                                private readonly TeacherStringConverter $teacherStringConverter) {

    }

    public function validate(mixed $value, Constraint $constraint): void {
        if(!$value instanceof ParentsDayAppointment) {
            throw new UnexpectedTypeException($value, ParentsDayAppointment::class);
        }

        if(!$constraint instanceof NoParentsDayAppointmentCollision) {
            throw new UnexpectedTypeException($constraint, NoParentsDayAppointmentCollision::class);
        }

        $parentsDay = $value->getParentsDay();

        foreach($value->getStudents() as $student) {
            $appointments = $this->appointmentRepository->findForStudents([$student], $parentsDay);

            foreach($appointments as $appointment) {
                if($appointment->getId() === $value->getId()) {
                    continue;
                }

                if($this->areAppointmentsOverlapping($appointment, $value)) {
                    $this->context
                        ->buildViolation($constraint->studentMessage)
                        ->setParameter('{{ student }}', $this->studentStringConverter->convert($student))
                        ->addViolation();;
                }
            }
        }

        foreach($value->getTeachers() as $teacher) {
            $appointments = $this->appointmentRepository->findForTeacher($teacher, $parentsDay);

            foreach($appointments as $appointment) {
                if($appointment->getId() === $value->getId()) {
                    continue;
                }

                if($this->areAppointmentsOverlapping($appointment, $value)) {
                    $this->context
                        ->buildViolation($constraint->teacherMessage)
                        ->setParameter('{{ teacher }}', $this->teacherStringConverter->convert($teacher))
                        ->addViolation();;
                }
            }
        }
    }

    private function areAppointmentsOverlapping(ParentsDayAppointment $appointmentA, ParentsDayAppointment $appointmentB): bool {
        return $appointmentA->getStart() < $appointmentB->getEnd() && $appointmentB->getStart() < $appointmentA->getEnd();
    }
}