<?php

namespace App\Common\Sorting;

use App\Common\Grouping\TeacherTuitionsGroup;
use App\Common\Sorting\TeacherStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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