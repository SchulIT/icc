<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamViewItem extends AbstractViewItem {

    private $exam;

    public function __construct(Exam $exam) {
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