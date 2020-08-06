<?php

namespace App\Sorting;

class StringStrategy implements SortingStrategyInterface {

    public function compare($objectA, $objectB): int {
        return strnatcasecmp((string)$objectA, (string)$objectB);
    }
}