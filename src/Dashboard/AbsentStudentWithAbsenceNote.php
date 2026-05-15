<?php

namespace App\Dashboard;

use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\Student;

class AbsentStudentWithAbsenceNote extends AbsentStudent {

    public function __construct(Student $student, private StudentAbsence $absence) {
        parent::__construct($student, AbsenceReason::Absence);
    }

    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}