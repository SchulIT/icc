<?php

namespace App\Sorting;

use App\Grouping\StringGroup;

class StringGroupStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $stringStrategy) {
        $this->stringStrategy = $stringStrategy;
    }

    /**
     * @param StringGroup $objectA
     * @param StringGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getKey(), $objectB->getKey());
    }
}