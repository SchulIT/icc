<?php

namespace App\Timetable\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Timetable\Entity\TimetableLesson;

class TimetableLessonStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) { }

    /**
     * @param TimetableLesson $objectA
     * @param TimetableLesson $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $compareDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($compareDate === 0) {
            return $objectA->getLessonStart() - $objectB->getLessonStart();
        }

        return $compareDate;
    }
}