<?php

namespace App\Sorting;

use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;

class GradeTeacherStrategy implements SortingStrategyInterface {

    private $teacherStrategy;

    public function __construct(TeacherStrategy $teacherStrategy) {
        $this->teacherStrategy = $teacherStrategy;
    }

    /**
     * @param GradeTeacher $objectA
     * @param GradeTeacher $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $typeA = $objectA->getType()->equals(GradeTeacherType::Primary()) ? 1 : 0;
        $typeB = $objectB->getType()->equals(GradeTeacherType::Primary()) ? 1 : 0;

        if($typeA === $typeB) {
            return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
        }

        return $typeB - $typeA;
    }
}