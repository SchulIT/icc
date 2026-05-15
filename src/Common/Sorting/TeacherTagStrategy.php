<?php

namespace App\Common\Sorting;

use App\Common\Entity\TeacherTag;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class TeacherTagStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param TeacherTag $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}