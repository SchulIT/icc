<?php

namespace App\Book\Sorting;

use App\Book\Lesson;
use App\Framework\Sorting\SortingStrategyInterface;

class LessonStrategy implements SortingStrategyInterface {

    /**
     * @param Lesson $objectA
     * @param Lesson $objectB
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLessonNumber() - $objectB->getLessonNumber();
    }

}