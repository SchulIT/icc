<?php

namespace App\Sorting;

use App\Entity\Tuition;

class TuitionStrategy implements SortingStrategyInterface {

    /**
     * @param Tuition $objectA
     * @param Tuition $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getName(), $objectB->getName());
    }
}