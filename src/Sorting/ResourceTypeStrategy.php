<?php

namespace App\Sorting;

use App\Entity\ResourceType;

class ResourceTypeStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param ResourceType $objectA
     * @param ResourceType $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}