<?php

namespace App\Sorting;

use App\Display\GradeGroup;

class DisplayGroupStrategy implements SortingStrategyInterface {
    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param GradeGroup $objectA
     * @param GradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getHeader(), $objectB->getHeader());
    }
}