<?php

namespace App\Sorting;

use App\Grouping\StudentAbsenceTuitionGroup;

class StudentAbsenceTuitionGroupStrategy implements SortingStrategyInterface {

    private TuitionStrategy $tuitionStrategy;

    public function __construct(TuitionStrategy $strategy) {
        $this->tuitionStrategy = $strategy;
    }

    /**
     * @param StudentAbsenceTuitionGroup $objectA
     * @param StudentAbsenceTuitionGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->tuitionStrategy->compare($objectA->getTuition(), $objectB->getTuition());
    }
}