<?php

namespace App\Common\Sorting;

use App\Common\Entity\ResourceEntity;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

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