<?php

namespace App\Dashboard;

use App\Entity\Student;

class AbsentStudent {

    private $student;
    private $reason;

    public function __construct(Student $student, AbsenceReason $reason) {
        $this->student = $student;
        $this->reason = $reason;
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