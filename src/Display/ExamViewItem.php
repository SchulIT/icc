<?php

namespace App\Display;

use App\Entity\Exam;

class ExamViewItem extends AbstractViewItem {

    private Exam $exam;

    public function __construct(Exam $exam) {
        parent::__construct($exam->getLessonStart(), false);

        $this->exam = $exam;
    }

    public function getExam(): Exam {
        return $this->exam;
    }

    public function getName(): string {
        return 'exam';
    }

    /**
     * @inheritDoc
     */
    public function getSortingIndex(): int {
        return 1;
    }
}