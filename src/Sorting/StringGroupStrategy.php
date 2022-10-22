<?php

namespace App\Sorting;

use App\Grouping\StringGroup;

class StringGroupStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param StringGroup $objectA
     * @param StringGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getKey(), $objectB->getKey());
    }
}