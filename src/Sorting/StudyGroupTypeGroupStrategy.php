<?php

namespace App\Sorting;

use App\Entity\StudyGroupType;
use App\Grouping\StudyGroupTypeGroup;

class StudyGroupTypeGroupStrategy implements SortingStrategyInterface {

    /**
     * @param StudyGroupTypeGroup $objectA
     * @param StudyGroupTypeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if ($objectA->getType()->equals($objectB->getType())) {
            return 0;
        } else {
            if ($objectA->getType()->equals(StudyGroupType::Grade())) {
                return -1;
            } else {
                return 1;
            }
        }
    }
}