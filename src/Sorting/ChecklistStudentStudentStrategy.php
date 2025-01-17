<?php

namespace App\Sorting;

use App\Entity\ChecklistStudent;

readonly class ChecklistStudentStudentStrategy implements SortingStrategyInterface {

    public function __construct(private StudentStrategy $strategy) {

    }

    /**
     * @param ChecklistStudent $objectA
     * @param ChecklistStudent $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->strategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}