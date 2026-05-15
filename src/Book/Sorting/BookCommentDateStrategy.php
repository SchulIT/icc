<?php

namespace App\Book\Sorting;

use App\Book\Entity\BookComment;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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