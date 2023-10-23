<?php

namespace App\Exam;

use App\Entity\Exam;

class Reassignments {

    /**
     * @param Exam[] $examsToAdd
     * @param Exam[] $examsToRemove
     * @param Exam[] $unchangedExams
     */
    public function __construct(private readonly array $examsToAdd, private readonly array $examsToRemove, private readonly array $unchangedExams) {

    }

    /**
     * @return Exam[]
     */
    public function getExamsToAdd(): array {
        return $this->examsToAdd;
    }

    /**
     * @return Exam[]
     */
    public function getExamsToRemove(): array {
        return $this->examsToRemove;
    }

    /**
     * @return Exam[]
     */
    public function getUnchangedExams(): array {
        return $this->unchangedExams;
    }
}