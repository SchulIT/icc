<?php

namespace App\Substitution\Sorting;

use App\Common\Sorting\GradeNameStrategy;
use App\Common\Grouping\SubstitutionGradeGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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