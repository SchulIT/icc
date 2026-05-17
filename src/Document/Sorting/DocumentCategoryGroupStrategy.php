<?php

namespace App\Document\Sorting;

use App\Document\Grouping\DocumentCategoryGroup;
use App\Document\Sorting\DocumentCategoryStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

readonly class DocumentCategoryGroupStrategy implements SortingStrategyInterface {

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