<?php

namespace App\Framework\Sorting;

use App\Framework\Grouping\StringGroup;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

/**
 * @implements SortingStrategyInterface<StringGroup>
 */
readonly class StringGroupStrategy implements SortingStrategyInterface {

    public function __construct(
        private StringStrategy $stringStrategy
    ) { }

    /**
     * @param StringGroup $objectA
     * @param StringGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getKey(), $objectB->getKey());
    }
}