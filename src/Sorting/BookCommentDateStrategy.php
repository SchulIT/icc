<?php

namespace App\Sorting;

use App\Entity\BookComment;

class BookCommentDateStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) {

    }

    /**
     * @param BookComment $objectA
     * @param BookComment $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}