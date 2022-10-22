<?php

namespace App\Sorting;

use App\Entity\Subject;

class SubjectAbbreviationStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param Subject $objectA
     * @param Subject $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getAbbreviation(), $objectB->getAbbreviation());
    }
}