<?php

namespace App\Common\Sorting;

use App\Common\Sorting\GradeNameStrategy;
use App\Common\Grouping\StudentGradeGroup;
use App\Framework\Sorting\SortingStrategyInterface;

class StudentGradeGroupStrategy implements SortingStrategyInterface {

    public function __construct(private GradeNameStrategy $gradeStrategy)
    {
    }

    /**
     * @param StudentGradeGroup $objectA
     * @param StudentGradeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}