<?php

namespace App\Sorting;

use App\Display\AbstractViewItem;

class DisplayViewItemStrategy implements SortingStrategyInterface {

    /**
     * @param AbstractViewItem $objectA
     * @param AbstractViewItem $objectB
     */
    public function compare($objectA, $objectB): int {
        $lessonStartCmp = $objectA->getLesson() - $objectB->getLesson();

        if($lessonStartCmp === 0) {
            $typeCmp = $objectA->getSortingIndex() - $objectB->getSortingIndex();

            if($typeCmp !== 0) {
                return $typeCmp;
            }

            return (int)$objectB->isStartsBefore() - (int)$objectA->isStartsBefore();
        }

        return $lessonStartCmp;
    }
}