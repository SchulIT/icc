<?php

namespace App\Common\Sorting;

use App\Common\Entity\Section;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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