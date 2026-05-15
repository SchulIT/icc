<?php

namespace App\Common\Sorting;

use App\Common\Entity\Teacher;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class TeacherStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Teacher $object
     */
    protected function getValue($object): string {
        return $object->getAcronym();
    }
}