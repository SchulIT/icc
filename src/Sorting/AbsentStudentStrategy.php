<?php

namespace App\Sorting;

use App\Dashboard\AbsentStudent;

class AbsentStudentStrategy implements SortingStrategyInterface {

    public function __construct(private StudentStrategy $studentStrategy)
    {
    }

    /**
     * @param AbsentStudent $objectA
     * @param AbsentStudent $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}