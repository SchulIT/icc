<?php

namespace App\Display;

use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamSupervision;

class ExamSupervisionViewItem extends AbstractViewItem {

    private ExamSupervision $supervision;

    public function __construct(ExamSupervision $supervision) {
        parent::__construct($supervision->getLesson(), false);

        $this->supervision = $supervision;
    }

    public function getSupervision(): ExamSupervision {
        return $this->supervision;
    }

    public function getExam(): Exam {
        return $this->supervision->getExam();
    }

    public function getName(): string {
        return 'exam_supervision';
    }

    public function getSortingIndex(): int {
        return 2;
    }
}