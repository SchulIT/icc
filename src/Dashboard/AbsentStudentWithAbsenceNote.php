<?php

namespace App\Dashboard;

use App\Entity\StudentAbsence;
use App\Entity\Student;

class AbsentStudentWithAbsenceNote extends AbsentStudent {

    private StudentAbsence $absence;

    public function __construct(Student $student, StudentAbsence $absence) {
        parent::__construct($student, AbsenceReason::Absence());

        $this->absence = $absence;
    }

    /**
     * @return StudentAbsence
     */
    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}