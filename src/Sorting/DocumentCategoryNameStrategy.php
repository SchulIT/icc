<?php

namespace App\Sorting;

use App\Entity\DocumentCategory;

class DocumentCategoryNameStrategy implements SortingStrategyInterface {
    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param DocumentCategory $objectA
     * @param DocumentCategory $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare(
            $objectA->getName(),
            $objectB->getName()
        );
    }
}