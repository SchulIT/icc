<?php

namespace App\Exam;

use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamStudent;

class ExamSplitResult {

    /**
     * @param Exam[] $exams
     * @param ExamStudent[] $studentsNotMatched
     */
    public function __construct(public readonly array $exams, public readonly array $studentsNotMatched) {

    }
}