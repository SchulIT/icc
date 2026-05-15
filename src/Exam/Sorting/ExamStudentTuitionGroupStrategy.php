<?php

namespace App\Exam\Sorting;

use App\Exam\Grouping\ExamStudentTuitionGroup;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\TuitionStrategy;

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