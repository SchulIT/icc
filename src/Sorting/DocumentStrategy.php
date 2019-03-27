<?php

namespace App\Sorting;

use App\Entity\Document;

class DocumentStrategy implements SortingStrategyInterface {

    /**
     * @param Document $objectA
     * @param Document $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getName(), $objectB->getName());
    }
}