<?php

namespace App\Sorting;

use App\Grouping\MessageExpirationGroup;

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