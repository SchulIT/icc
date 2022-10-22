<?php

namespace App\Sorting;

use App\Entity\Message;
use App\Entity\MessagePriority;

class MessageStrategy implements SortingStrategyInterface {

    private function getPriorityMap() {
        return [
            MessagePriority::Normal()->getValue() => 3,
            MessagePriority::Important()->getValue() => 2,
            MessagePriority::Emergency()->getValue() => 1
        ];
    }

    /**
     * @param Message $objectA
     * @param Message $objectB
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getPriority()->equals($objectB->getPriority()) !== true) {
            $map = $this->getPriorityMap();

            return $map[$objectA->getPriority()->getValue()] - $map[$objectB->getPriority()->getValue()];
        }

        return $objectA->getExpireDate()->getTimestamp() - $objectB->getExpireDate()->getTimestamp();
    }
}