<?php

namespace App\Sorting;

use App\Grouping\StudyGroupGradeGroup;

class TuitionGradeGroupStrategy implements SortingStrategyInterface {

    /**
     * @param StudyGroupGradeGroup $objectA
     * @param StudyGroupGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getGrade()->getName(), $objectB->getGrade()->getName());
    }
}