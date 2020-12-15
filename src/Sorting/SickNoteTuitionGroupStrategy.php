<?php

namespace App\Sorting;

use App\Grouping\SickNoteTuitionGroup;

class SickNoteTuitionGroupStrategy implements SortingStrategyInterface {

    private $tuitionStrategy;

    public function __construct(TuitionStrategy $strategy) {
        $this->tuitionStrategy = $strategy;
    }

    /**
     * @param SickNoteTuitionGroup $objectA
     * @param SickNoteTuitionGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->tuitionStrategy->compare($objectA->getTuition(), $objectB->getTuition());
    }
}