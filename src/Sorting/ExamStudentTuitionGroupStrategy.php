<?php

namespace App\Sorting;

use App\Grouping\ExamStudentTuitionGroup;

class ExamStudentTuitionGroupStrategy implements SortingStrategyInterface {

    public function __construct(private readonly TuitionStrategy $tuitionStrategy) {

    }

    /**
     * @param ExamStudentTuitionGroup $objectA
     * @param ExamStudentTuitionGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getTuition() === null && $objectB->getTuition() === null) {
            return 0;
        } else if($objectA->getTuition() === null && $objectB->getTuition() !== null) {
            return -1;
        } else if($objectA->getTuition() !== null && $objectB->getTuition() === null) {
            return 1;
        } else {
            return $this->tuitionStrategy->compare($objectA->getTuition(), $objectB->getTuition());
        }
    }
}