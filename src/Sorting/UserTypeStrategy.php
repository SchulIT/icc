<?php

namespace App\Sorting;

use App\Entity\UserType;

class UserTypeStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param UserType $object
     */
    protected function getValue($object): string {
        return $object->getValue();
    }
}