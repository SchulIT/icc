<?php

namespace App\Sorting;

use App\Grouping\StudentGradeGroup;

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