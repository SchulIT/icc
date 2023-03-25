<?php

namespace App\Sorting;

use App\Entity\Message;

class MessageExpiryDateStrategy implements SortingStrategyInterface {

    /**
     * @param Message $objectA
     * @param Message $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getExpireDate()->getTimestamp() - $objectB->getExpireDate()->getTimestamp();
    }
}