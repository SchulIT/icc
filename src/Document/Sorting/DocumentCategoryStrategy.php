<?php

namespace App\Document\Sorting;

use App\Document\Entity\DocumentCategory;
use App\Framework\Sorting\SortingStrategyInterface;

class DocumentCategoryStrategy implements SortingStrategyInterface {

    /**
     * @param DocumentCategory $objectA
     * @param DocumentCategory $objectB
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getName(), $objectB->getName());
    }
}