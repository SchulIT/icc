<?php

namespace App\Document\Sorting;

use App\Document\Grouping\DocumentUserTypeGroup;
use App\Framework\Sorting\SortingStrategyInterface;

class DocumentUserTypeGroupStragegy implements SortingStrategyInterface {

    /**
     * @param DocumentUserTypeGroup $objectA
     * @param DocumentUserTypeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getUserType()->value, $objectB->getUserType()->value);
    }
}