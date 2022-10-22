<?php

namespace App\Dashboard;

use App\Entity\Student;

class AbsentStudent {

    public function __construct(private Student $student, private AbsenceReason $reason)
    {
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return AbsenceReason
     */
    public function getReason(): AbsenceReason {
        return $this->reason;
    }
}