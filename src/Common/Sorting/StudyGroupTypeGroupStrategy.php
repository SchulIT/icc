<?php

namespace App\Common\Sorting;

use App\Common\Entity\StudyGroupType;
use App\Common\Grouping\StudyGroupTypeGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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