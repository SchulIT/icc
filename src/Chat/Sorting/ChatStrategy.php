<?php

namespace App\Chat\Sorting;

use App\Chat\Entity\Chat;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

class ChatStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) {

    }

    /**
     * @param Chat $objectA
     * @param Chat $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getMessages()->last()->getCreatedAt(), $objectB->getMessages()->last()->getCreatedAt());
    }
}