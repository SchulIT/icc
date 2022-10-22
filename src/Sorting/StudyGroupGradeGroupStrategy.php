<?php

namespace App\Sorting;

use App\Grouping\StudyGroupGradeGroup;

class StudyGroupGradeGroupStrategy implements SortingStrategyInterface {

    public function __construct(private GradeNameStrategy $gradeStrategy)
    {
    }

    /**
     * @param StudyGroupGradeGroup $objectA
     * @param StudyGroupGradeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}