<?php

namespace App\Sorting;

use App\Entity\Teacher;

class TeacherStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Teacher $object
     */
    protected function getValue($object): string {
        return $object->getAcronym();
    }
}