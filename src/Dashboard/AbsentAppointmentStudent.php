<?php

namespace App\Dashboard;

use App\Entity\Appointment;
use App\Entity\Student;

class AbsentAppointmentStudent extends AbsentStudent {
    public function __construct(Student $student, private Appointment $appointment) {
        parent::__construct($student, AbsenceReason::Appointment());
    }

    public function getAppointment(): Appointment {
        return $this->appointment;
    }
}