<?php

namespace App\Sorting;

use App\Entity\StudyGroupType;
use App\Grouping\StudyGroupTypeGroup;

class StudyGroupTypeGroupStrategy implements SortingStrategyInterface {

    /**
     * @param StudyGroupTypeGroup $objectA
     * @param StudyGroupTypeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        if ($objectA->getType() === $objectB->getType()) {
            return 0;
        } else {
            if ($objectA->getType() === StudyGroupType::Grade) {
                return -1;
            } else {
                return 1;
            }
        }
    }
}