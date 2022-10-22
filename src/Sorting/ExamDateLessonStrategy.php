<?php

namespace App\Sorting;

use App\Entity\Exam;

class ExamDateLessonStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param Exam $objectA
     * @param Exam $objectB
     */
    public function compare($objectA, $objectB): int {
        $cmpDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($cmpDate !== 0) {
            return $cmpDate;
        }

        return $objectB->getLessonStart() - $objectA->getLessonStart();
    }
}