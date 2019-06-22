<?php

namespace App\Sorting;

use App\Entity\Grade;

class GradeNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Grade $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}