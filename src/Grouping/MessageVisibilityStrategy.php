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
    public function computeKey($object, array $options = [ ]) {
        return $object->getVisibilities()->map(function (UserTypeEntity $visibility) {
            return $visibility->getUserType();
        })->toArray();
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param UserType $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new MessageVisibilityGroup($key);
    }
}