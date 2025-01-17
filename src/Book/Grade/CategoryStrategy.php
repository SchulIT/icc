<?php

namespace App\Book\Grade;

use App\Sorting\SortingStrategyInterface;
use App\Sorting\TuitionGradeCategoryStrategy;

class CategoryStrategy implements SortingStrategyInterface {

    public function __construct(private readonly TuitionGradeCategoryStrategy $strategy) {

    }

    /**
     * @param Category $objectA
     * @param Category $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->strategy->compare($objectA->getCategory(), $objectB->getCategory());
    }
}