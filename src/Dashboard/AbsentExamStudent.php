<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;

class AbsentExamStudent extends AbsentStudent {

    public function __construct(Student $student, private readonly Exam $exam, private readonly ?Tuition $tuition) {
        parent::__construct($student, AbsenceReason::Exam);
    }

    public function getExam(): Exam {
        return $this->exam;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }
}