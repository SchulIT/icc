<?php

namespace App\Substitution\Sorting;

use App\Common\Sorting\TeacherStrategy;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Substitution\Grouping\SubstitutionTeacherGroup;

class SubstitutionTeacherGroupStrategy implements SortingStrategyInterface {

    public function __construct(private TeacherStrategy $teacherStrategy)
    {
    }

    /**
     * @param SubstitutionTeacherGroup $objectA
     * @param SubstitutionTeacherGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}