<?php

namespace App\Sorting;

use App\Grouping\DocumentCategoryGroup;

class DocumentCategoryGroupStrategy implements SortingStrategyInterface {

    public function __construct(private DocumentCategoryStrategy $categoryStrategy)
    {
    }

    /**
     * @param DocumentCategoryGroup $objectA
     * @param DocumentCategoryGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->categoryStrategy->compare($objectA->getCategory(), $objectB->getCategory());
    }
}