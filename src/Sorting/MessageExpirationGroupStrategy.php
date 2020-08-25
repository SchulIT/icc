<?php

namespace App\Sorting;

use App\Grouping\MessageExpirationGroup;

class MessageExpirationGroupStrategy implements SortingStrategyInterface {

    /**
     * @param MessageExpirationGroup $objectA
     * @param MessageExpirationGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->isExpired()) {
            return 1;
        }

        return -1;
    }
}