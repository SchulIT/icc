<?php

namespace App\Sorting;

use App\Grouping\StudentAbsenceGradeGroup;

class StudentAbsenceGradeGroupStrategy implements SortingStrategyInterface {

    private GradeNameStrategy $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeNameStrategy) {
        $this->gradeStrategy = $gradeNameStrategy;
    }

    /**
     * @param StudentAbsenceGradeGroup $objectA
     * @param StudentAbsenceGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}