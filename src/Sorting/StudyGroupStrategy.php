<?php

namespace App\Sorting;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

class StudyGroupStrategy implements SortingStrategyInterface {

    /**
     * @param StudyGroup $objectA
     * @param StudyGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $gradeType = StudyGroupType::Grade();

        if($objectA->getType()->equals($gradeType) && $objectB->getType()->equals($gradeType)) {
            return strnatcmp($objectA->getName(), $objectB->getName());
        } else if($objectA->getType()->equals($gradeType)) {
            return -1;
        } else if($objectB->getType()->equals($gradeType)) {
            return 1;
        }

        return strnatcmp($objectA->getName(), $objectB->getName());
    }
}