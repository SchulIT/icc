<?php

namespace App\Sorting;

use App\Entity\Section;

class SectionDateStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $strategy) { }

    /**
     * @param Section $objectA
     * @param Section $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->strategy->compare($objectA->getStart(), $objectB->getStart());
    }
}