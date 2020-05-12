<?php

namespace App\Sorting;

use App\Entity\Subject;

class SubjectAbbreviationStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $stringStrategy) {
        $this->stringStrategy = $stringStrategy;
    }

    /**
     * @param Subject $objectA
     * @param Subject $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getAbbreviation(), $objectB->getAbbreviation());
    }
}