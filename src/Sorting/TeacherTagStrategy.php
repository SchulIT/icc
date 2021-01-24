<?php

namespace App\Sorting;

use App\Entity\TeacherTag;

class TeacherTagStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param TeacherTag $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}