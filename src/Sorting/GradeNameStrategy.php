<?php

namespace App\Sorting;

use App\Entity\Grade;

class GradeNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Grade|null $object
     */
    protected function getValue($object): string {
        if($object === null) {
            return '';
        }

        return $object->getName();
    }
}