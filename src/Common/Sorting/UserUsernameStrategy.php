<?php

namespace App\Common\Sorting;

use App\Common\Entity\User;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class UserUsernameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param User $object
     */
    protected function getValue($object): string {
        return $object->getUsername();
    }
}