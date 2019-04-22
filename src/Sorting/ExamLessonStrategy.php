<?php

namespace App\Sorting;

use App\Entity\Exam;

class ExamLessonStrategy implements SortingStrategyInterface {

    /**
     * @param Exam $objectA
     * @param Exam $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLessonStart() - $objectB->getLessonStart();
    }
}