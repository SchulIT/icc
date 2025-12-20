<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamViewItem extends AdditionalExtraAwareViewItem {

    public function __construct(private Exam $exam, array $absentStudentGroups, array $studentInfo, bool $hasAnyStudentWithHealthInfo) {
        parent::__construct($absentStudentGroups, $studentInfo, $hasAnyStudentWithHealthInfo);
    }

    public function getExam(): Exam {
        return $this->exam;
    }

    public function getBlockName(): string {
        return 'exam';
    }
}