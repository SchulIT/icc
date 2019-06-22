<?php

namespace App\Sorting;

use App\Grouping\StudentGradeGroup;

class StudentGradeGroupStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeStrategy) {
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param StudentGradeGroup $objectA
     * @param StudentGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}