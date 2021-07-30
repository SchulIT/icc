<?php

namespace App\Sorting;

use App\Book\Lesson;

class LessonStrategy implements SortingStrategyInterface {

    /**
     * @param Lesson $objectA
     * @param Lesson $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLessonNumber() - $objectB->getLessonNumber();
    }

}