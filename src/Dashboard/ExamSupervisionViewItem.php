<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Room;
use App\Grouping\AbsentStudentGroup;

class ExamSupervisionViewItem extends AbsenceAwareViewItem {

    /** @var Exam[] */
    private array $exams = [ ];

    /**
     * @param Exam|Exam[] $examOrExams
     * @param AbsentStudentGroup[] $absentStudentGroups
     */
    public function __construct($examOrExams, array $absentStudentGroups) {
        parent::__construct($absentStudentGroups);

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

    /**
     * @return Room[]
     */
    public function getRooms():array {
        return array_unique(
            array_map(
                fn(Exam $exam) => $exam->getRoom(),
                $this->exams
            )
        );
    }

    public function getBlockName(): string {
        return 'exam_supervision';
    }
}