<?php

namespace App\Sorting;

use App\Entity\DocumentCategory;

class DocumentCategoryStrategy implements SortingStrategyInterface {

    /**
     * @param DocumentCategory $objectA
     * @param DocumentCategory $objectB
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getName(), $objectB->getName());
    }
}