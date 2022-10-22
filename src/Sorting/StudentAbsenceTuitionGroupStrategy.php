<?php

namespace App\Sorting;

use App\Grouping\StudentAbsenceTuitionGroup;

class StudentAbsenceTuitionGroupStrategy implements SortingStrategyInterface {

    public function __construct(private TuitionStrategy $tuitionStrategy)
    {
    }

    /**
     * @param StudentAbsenceTuitionGroup $objectA
     * @param StudentAbsenceTuitionGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->tuitionStrategy->compare($objectA->getTuition(), $objectB->getTuition());
    }
}