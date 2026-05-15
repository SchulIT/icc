<?php

namespace App\Common\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Grade\Entity\TuitionGradeCategory;

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