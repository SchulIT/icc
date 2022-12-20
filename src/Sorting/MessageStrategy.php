<?php

namespace App\Sorting;

use App\Entity\Message;
use App\Entity\MessagePriority;

class MessageStrategy implements SortingStrategyInterface {

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

        return $objectA->getExpireDate()->getTimestamp() - $objectB->getExpireDate()->getTimestamp();
    }
}