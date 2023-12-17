<?php

namespace App\Sorting;

use App\Entity\ExamStudent;

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