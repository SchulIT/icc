<?php

namespace App\Sorting;

use App\Entity\Resource;

class ResourceStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param Resource $objectA
     * @param Resource $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}