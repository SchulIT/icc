<?php

namespace App\Sorting;

use App\Grouping\TeacherTuitionsGroup;

class TeacherTuitionsGroupStrategy implements SortingStrategyInterface {

    public function __construct(private readonly TeacherStrategy $teacherStrategy) { }

    /**
     * @param TeacherTuitionsGroup $objectA
     * @param TeacherTuitionsGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}