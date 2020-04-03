<?php

namespace App\Grouping;

use App\Entity\Message;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;

class MessageVisibilityStrategy implements GroupingStrategyInterface {

    /**
     * @param Message $object
     * @return UserType[]
     */
    public function computeKey($object) {
        return $object->getVisibilities()->map(function (UserTypeEntity $visibility) {
            return $visibility->getUserType();
        })->toArray();
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
        return new MessageVisibilityGroup($key);
    }
}