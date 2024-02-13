<?php

namespace App\Event;

use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ParentsDayAppointmentCancelledEvent extends Event {
    public function __construct(private readonly ParentsDayAppointment $appointment, private readonly Student $student, private readonly User $initiator) {

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

    /**
     * @return User
     */
    public function getInitiator(): User {
        return $this->initiator;
    }
}