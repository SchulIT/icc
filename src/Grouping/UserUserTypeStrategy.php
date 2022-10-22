<?php

namespace App\Grouping;

use App\Entity\User;
use App\Entity\UserType;

class UserUserTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param User $object
     * @return UserType
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getUserType();
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param UserType $key
     * @return UserUserTypeGroup
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new UserUserTypeGroup($key);
    }
}