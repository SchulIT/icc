<?php

namespace App\Dashboard\Sorting;

use App\Dashboard\AbsentStudent;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\StudentStrategy;

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