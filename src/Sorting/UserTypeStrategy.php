<?php

namespace App\Sorting;

use App\Entity\UserType;

class UserTypeStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param UserType $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getValue();
    }
}