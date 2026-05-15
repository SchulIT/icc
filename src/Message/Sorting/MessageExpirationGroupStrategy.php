<?php

namespace App\Message\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Message\Grouping\MessageExpirationGroup;

class MessageExpirationGroupStrategy implements SortingStrategyInterface {

    /**
     * @param MessageExpirationGroup $objectA
     * @param MessageExpirationGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        if($objectA->isExpired()) {
            return 1;
        }

        return -1;
    }
}