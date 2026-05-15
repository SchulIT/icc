<?php

namespace App\Appointment\Event;

use App\Appointment\Entity\Appointment;
use App\Common\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class AppointmentConfirmedEvent extends Event {

    public function __construct(private Appointment $appointment, private User $confirmedBy)
    {
    }

    public function getAppointment(): Appointment {
        return $this->appointment;
    }

    public function getConfirmedBy(): User {
        return $this->confirmedBy;
    }
}