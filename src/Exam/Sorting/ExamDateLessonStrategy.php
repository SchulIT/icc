<?php

namespace App\Exam\Sorting;

use App\Exam\Entity\Exam;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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