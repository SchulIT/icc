<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamViewItem extends AbsenceAwareViewItem {

    public function __construct(private Exam $exam, array $absentStudentGroups) {
        parent::__construct($absentStudentGroups);
    }

    public function getExam(): Exam {
        return $this->exam;
    }

    public function getBlockName(): string {
        return 'exam';
    }
}