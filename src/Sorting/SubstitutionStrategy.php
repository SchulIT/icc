<?php

namespace App\Sorting;

use App\Entity\Substitution;

class SubstitutionStrategy implements SortingStrategyInterface {


    /**
     * @param Substitution $objectA
     * @param Substitution $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLessonStart() - $objectB->getLessonStart();
    }
}