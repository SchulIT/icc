<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Student;

class AbsentExamStudent extends AbsentStudent {

    private $exam;

    public function __construct(Student $student, Exam $exam) {
        parent::__construct($student, AbsenceReason::Exam());

        $this->exam = $exam;
    }

    /**
     * @return Exam
     */
    public function getExam(): Exam {
        return $this->exam;
    }
}