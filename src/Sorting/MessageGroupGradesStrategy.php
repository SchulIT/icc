<?php

namespace App\Sorting;

use App\Grouping\MessageGradeGroup;

class MessageGroupGradesStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeStrategy) {
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param MessageGradeGroup $objectA
     * @param MessageGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}