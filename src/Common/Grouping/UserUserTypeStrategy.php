<?php

namespace App\Common\Grouping;

use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Grouping\UserUserTypeGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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
        return $keyA === $keyB;
    }

    /**
     * @param UserType $key
     * @return UserUserTypeGroup
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new UserUserTypeGroup($key);
    }
}