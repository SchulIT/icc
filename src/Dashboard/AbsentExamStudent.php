<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Student;

class AbsentExamStudent extends AbsentStudent {

    public function __construct(Student $student, private Exam $exam) {
        parent::__construct($student, AbsenceReason::Exam);
    }

    public function getExam(): Exam {
        return $this->exam;
    }
}