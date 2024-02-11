<?php

namespace App\Event;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use Symfony\Contracts\EventDispatcher\Event;

class ParentsDayAppointmentCreatedEvent extends Event {
    public function __construct(private readonly ParentsDayAppointment $appointment, private readonly Student $student) {

    }

    /**
     * @return ParentsDayAppointment
     */
    public function getAppointment(): ParentsDayAppointment {
        return $this->appointment;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }
}