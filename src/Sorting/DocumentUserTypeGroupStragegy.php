<?php

namespace App\Sorting;

use App\Grouping\DocumentUserTypeGroup;

class DocumentUserTypeGroupStragegy implements SortingStrategyInterface {

    /**
     * @param DocumentUserTypeGroup $objectA
     * @param DocumentUserTypeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getUserType()->value, $objectB->getUserType()->value);
    }
}