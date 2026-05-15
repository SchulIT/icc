<?php

namespace App\StudentAbsence\Sorting;

use App\Common\Sorting\GradeNameStrategy;
use App\Framework\Sorting\SortingStrategyInterface;
use App\StudentAbsence\Grouping\StudentAbsenceGradeGroup;

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