<?php

namespace App\Common\Sorting;

use App\Common\Entity\ResourceType;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

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