<?php

namespace App\Display\Sorting;

use App\Display\GradeGroup;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

class DisplayGroupStrategy implements SortingStrategyInterface {
    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param GradeGroup $objectA
     * @param GradeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getHeader(), $objectB->getHeader());
    }
}