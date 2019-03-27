<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Message;
use App\Entity\MessageVisibility;
use App\Entity\UserType;

class MessageGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param Message $object
     * @return Grade[]
     */
    public function computeKey($object) {
        return array_map(function(MessageVisibility $visibility) {
            return $visibility->getUserType();
        }, $object->getVisibilities()->toArray());
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param UserType $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {

    }
}