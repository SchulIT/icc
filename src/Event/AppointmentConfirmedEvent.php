<?php

namespace App\Event;

use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class AppointmentConfirmedEvent extends Event {

    private $appointment;
    private $confirmedBy;

    public function __construct(Appointment $appointment, User $confirmedBy) {
        $this->appointment = $appointment;
        $this->confirmedBy = $confirmedBy;
    }

    /**
     * @return Appointment
     */
    public function getAppointment(): Appointment {
        return $this->appointment;
    }

    /**
     * @return User
     */
    public function getConfirmedBy(): User {
        return $this->confirmedBy;
    }
}