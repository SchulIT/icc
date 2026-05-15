<?php

namespace App\Exam\Sorting;

use App\Exam\Entity\ExamStudent;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\StudentStrategy;

class ExamStudentStrategy implements SortingStrategyInterface {

    public function __construct(private readonly StudentStrategy $studentStrategy) {

    }

    /**
     * @param ExamStudent $objectA
     * @param ExamStudent $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}