<?php

namespace App\Sorting;

use App\Entity\ResourceEntity;

class ResourceStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param ResourceEntity $objectA
     * @param ResourceEntity $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}