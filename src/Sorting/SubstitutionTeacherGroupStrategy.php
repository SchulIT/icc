<?php

namespace App\Sorting;

use App\Grouping\SubstitutionTeacherGroup;

class SubstitutionTeacherGroupStrategy implements SortingStrategyInterface {

    private $teacherStrategy;

    public function __construct(TeacherStrategy $teacherStrategy) {
        $this->teacherStrategy = $teacherStrategy;
    }

    /**
     * @param SubstitutionTeacherGroup $objectA
     * @param SubstitutionTeacherGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}