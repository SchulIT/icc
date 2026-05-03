<?php

namespace App\Sorting;

use App\Entity\ParentsDayTeacherRoom;
use Override;

class ParentsDayTeacherRoomTeacherStrategy implements SortingStrategyInterface {

    public function __construct(
        private readonly TeacherStrategy $teacherStrategy
    ) {

    }

    /**
     * @param ParentsDayTeacherRoom $objectA
     * @param ParentsDayTeacherRoom $objectB
     * @return int
     */
    #[Override]
    public function compare($objectA, $objectB): int {
        return $this->teacherStrategy->compare($objectA->getTeacher(), $objectB->getTeacher());
    }
}