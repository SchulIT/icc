<?php

namespace App\Message\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Message\Entity\Message;
use App\Message\Entity\MessagePriority;
use App\Message\Sorting\MessageExpiryDateStrategy;

class MessageStrategy implements SortingStrategyInterface {

    public function __construct(private readonly MessageExpiryDateStrategy $strategy) { }

    private function getPriorityMap() {
        return [
            MessagePriority::Normal->value => 3,
            MessagePriority::Important->value => 2,
            MessagePriority::Emergency->value => 1
        ];
    }

    /**
     * @param Message $objectA
     * @param Message $objectB
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getPriority() !== $objectB->getPriority()) {
            $map = $this->getPriorityMap();

            return $map[$objectA->getPriority()->value] - $map[$objectB->getPriority()->value];
        }

        return $this->strategy->compare($objectA, $objectB);
    }
}