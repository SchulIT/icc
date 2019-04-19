<?php

namespace App\Sorting;

class StringStrategy implements SortingStrategyInterface {

    public function compare($objectA, $objectB): int {
        return strnatcmp((string)$objectA, (string)$objectB);
    }
}