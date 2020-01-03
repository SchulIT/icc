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
        $lessonStartCmp = $objectA->getLessonStart() - $objectB->getLessonStart();

        if($lessonStartCmp === 0) {
            return (int)$objectB->startsBefore() - (int)$objectA->startsBefore();
        }

        return $lessonStartCmp;
    }
}