<?php

namespace App\Sorting;

use App\Entity\Exam;

class ExamDateLessonStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param Exam $objectA
     * @param Exam $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $cmpDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($cmpDate !== 0) {
            return $cmpDate;
        }

        return $objectB->getLessonStart() - $objectA->getLessonStart();
    }
}