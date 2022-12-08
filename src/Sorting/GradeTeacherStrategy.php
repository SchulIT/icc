<?php

namespace App\Sorting;

use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;

class GradeTeacherStrategy implements SortingStrategyInterface {

    public function __construct(private TeacherStrategy $teacherStrategy)
    {
    }

    /**
     * @param GradeTeacher $objectA
     * @param GradeTeacher $objectB
     */
    public function compare($objectA, $objectB): int {
        $typeA = $objectA->getType() === GradeTeacherType::Primary ? 1 : 0;
        $typeB = $objectB->getType() === GradeTeacherType::Primary ? 1 : 0;

        if($typeA === $typeB) {
            return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
        }

        return $typeB - $typeA;
    }
}