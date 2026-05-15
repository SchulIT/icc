<?php

namespace App\Substitution\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\TeacherStrategy;
use App\Substitution\Entity\Absence;

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