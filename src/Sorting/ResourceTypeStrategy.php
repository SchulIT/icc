<?php

namespace App\Sorting;

use App\Entity\ResourceType;

class ResourceTypeStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param ResourceType $objectA
     * @param ResourceType $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}