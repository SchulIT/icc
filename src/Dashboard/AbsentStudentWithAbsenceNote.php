<?php

namespace App\Dashboard;

use App\Entity\StudentAbsence;
use App\Entity\Student;

class AbsentStudentWithAbsenceNote extends AbsentStudent {

    public function __construct(Student $student, private StudentAbsence $absence) {
        parent::__construct($student, AbsenceReason::Absence());
    }

    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}