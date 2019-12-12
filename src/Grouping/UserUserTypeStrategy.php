<?php

namespace App\Grouping;

use App\Entity\User;
use App\Entity\UserType;

class UserUserTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param User $object
     * @return UserType
     */
    public function computeKey($object) {
        return $object->getUserType();
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
     * @return UserUserTypeGroup
     */
    public function createGroup($key): GroupInterface {
        return new UserUserTypeGroup($key);
    }
}