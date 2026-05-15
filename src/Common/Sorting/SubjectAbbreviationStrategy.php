<?php

namespace App\Common\Sorting;

use App\Common\Entity\Subject;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

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