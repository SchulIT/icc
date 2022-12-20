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
        return $object->getVisibilities()->map(fn(UserTypeEntity $visibility) => $visibility->getUserType())->toArray();
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param UserType $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new MessageVisibilityGroup($key);
    }
}