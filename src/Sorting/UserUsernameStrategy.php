<?php

namespace App\Sorting;

use App\Entity\User;

class UserUsernameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param User $object
     */
    protected function getValue($object): string {
        return $object->getUsername();
    }
}