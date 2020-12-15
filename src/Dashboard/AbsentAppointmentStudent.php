<?php

namespace App\Dashboard;

use App\Entity\Appointment;
use App\Entity\Student;

class AbsentAppointmentStudent extends AbsentStudent {
    /** @var Appointment */
    private $appointment;

    public function __construct(Student $student, Appointment $appointment) {
        parent::__construct($student, AbsenceReason::Appointment());

        $this->appointment = $appointment;
    }

    /**
     * @return Appointment
     */
    public function getAppointment(): Appointment {
        return $this->appointment;
    }
}