<?php

namespace App\Sorting;

use App\Grouping\SubstitutionTeacherGroup;

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