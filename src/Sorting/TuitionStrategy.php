<?php

namespace App\Sorting;

use App\Entity\Tuition;

class TuitionStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Tuition $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}