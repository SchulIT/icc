<?php

namespace App\Sorting;

use App\Entity\TuitionGradeCategory;

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