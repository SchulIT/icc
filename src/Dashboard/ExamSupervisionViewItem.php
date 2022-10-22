<?php

namespace App\Dashboard;

use App\Entity\Exam;

class ExamSupervisionViewItem extends AbstractViewItem {

    /** @var Exam[] */
    private array $exams = [ ];

    /**
     * @param Exam|Exam[] $examOrExams
     */
    public function __construct($examOrExams) {
        if(is_array($examOrExams)) {
            $this->exams = $examOrExams;
        } else {
            $this->exams[] = $examOrExams;
        }
    }

    public function getFirst(): ?Exam {
        return $this->exams[0] ?? null;
    }

    public function addExam(Exam $exam): void {
        $this->exams[] = $exam;
    }

    /**
     * @return Exam[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    public function getBlockName(): string {
        return 'exam_supervision';
    }
}