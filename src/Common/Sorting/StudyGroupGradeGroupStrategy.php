<?php

namespace App\Common\Sorting;

use App\Common\Sorting\GradeNameStrategy;
use App\Common\Grouping\StudyGroupGradeGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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