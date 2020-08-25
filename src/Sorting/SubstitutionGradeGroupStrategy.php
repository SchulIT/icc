<?php

namespace App\Sorting;

use App\Grouping\SubstitutionGradeGroup;

class SubstitutionGradeGroupStrategy implements SortingStrategyInterface {

    private $gradeStrategy;

    public function __construct(GradeNameStrategy $gradeStrategy) {
        $this->gradeStrategy = $gradeStrategy;
    }

    /**
     * @param SubstitutionGradeGroup $objectA
     * @param SubstitutionGradeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getGrade() === null) {
            return -1;
        } else if($objectB->getGrade() === null) {
            return 1;
        }

        return $this->gradeStrategy->compare($objectA->getGrade(), $objectB->getGrade());
    }
}