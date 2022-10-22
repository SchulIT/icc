<?php

namespace App\Sorting;

use App\Entity\Absence;

class AbsentTeacherStrategy implements SortingStrategyInterface {

    public function __construct(private TeacherStrategy $teacherStrategy)
    {
    }

    /**
     * @param Absence $objectA
     * @param Absence $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}