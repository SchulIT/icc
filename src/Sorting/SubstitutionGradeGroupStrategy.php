<?php

namespace App\Sorting;

use App\Grouping\SubstitutionGradeGroup;

class SubstitutionGradeGroupStrategy implements SortingStrategyInterface {

    public function __construct(private GradeNameStrategy $gradeStrategy)
    {
    }

    /**
     * @param SubstitutionGradeGroup $objectA
     * @param SubstitutionGradeGroup $objectB
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