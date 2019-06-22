<?php

namespace App\Sorting;

use App\Grouping\StudyGroupGradeGroup;

class StudyGroupGradeGroupStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeStrategy) {
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param StudyGroupGradeGroup $objectA
     * @param StudyGroupGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}