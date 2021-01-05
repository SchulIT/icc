<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamViewItem extends AbsenceAwareViewItem {

    private $exam;

    public function __construct(Exam $exam, array $absentStudentGroups) {
        parent::__construct($absentStudentGroups);

        $this->exam = $exam;
    }

    /**
     * @return Exam
     */
    public function getExam(): Exam {
        return $this->exam;
    }

    public function getBlockName(): string {
        return 'exam';
    }
}