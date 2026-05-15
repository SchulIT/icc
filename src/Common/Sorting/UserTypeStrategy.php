<?php

namespace App\Common\Sorting;

use App\Common\Entity\UserType;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class UserTypeStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param UserType $object
     */
    protected function getValue($object): string {
        return $object->value;
    }
}