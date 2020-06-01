<?php

namespace App\Sorting;

use App\Entity\Absence;

class AbsentTeacherStrategy implements SortingStrategyInterface {

    private $teacherStrategy;

    public function __construct(TeacherStrategy $teacherStrategy) {
        $this->teacherStrategy = $teacherStrategy;
    }

    /**
     * @param Absence $objectA
     * @param Absence $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}