<?php

namespace App\ParentsDay\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\ParentsDay\Entity\ParentsDayTeacherRoom;
use App\Common\Sorting\TeacherStrategy;
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