<?php

namespace App\Sorting;

use App\Grouping\DocumentCategoryGroup;

class DocumentCategoryGroupStrategy implements SortingStrategyInterface {

    private $categoryStrategy;

    public function __construct(DocumentCategoryStrategy $categoryStrategy) {
        $this->categoryStrategy = $categoryStrategy;
    }

    /**
     * @param DocumentCategoryGroup $objectA
     * @param DocumentCategoryGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->categoryStrategy->compare($objectA->getCategory(), $objectB->getCategory());
    }
}