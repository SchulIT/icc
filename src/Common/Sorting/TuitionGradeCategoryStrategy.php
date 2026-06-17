<?php

namespace App\Common\Sorting;

use App\Book\Entity\TuitionGradeCategory;
use App\Framework\Sorting\SortingStrategyInterface;

class TuitionGradeCategoryStrategy implements SortingStrategyInterface {


    /**
     * @param TuitionGradeCategory $objectA
     * @param TuitionGradeCategory $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getPosition() - $objectB->getPosition();
    }
}