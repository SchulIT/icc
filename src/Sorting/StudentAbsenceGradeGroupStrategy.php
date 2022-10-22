<?php

namespace App\Sorting;

use App\Grouping\StudentAbsenceGradeGroup;

class StudentAbsenceGradeGroupStrategy implements SortingStrategyInterface {

    public function __construct(private GradeNameStrategy $gradeStrategy)
    {
    }

    /**
     * @param StudentAbsenceGradeGroup $objectA
     * @param StudentAbsenceGradeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}