<?php

namespace App\Sorting;

use App\Grouping\SubstitutionGradeGroup;

class SubstitutionGradeGroupStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeStrategy $gradeStrategy) {
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param SubstitutionGradeGroup $objectA
     * @param SubstitutionGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}