<?php

namespace App\Common\Sorting;

use App\Common\Entity\Grade;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

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