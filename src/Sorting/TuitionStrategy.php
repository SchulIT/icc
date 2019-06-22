<?php

namespace App\Sorting;

use App\Entity\Tuition;

class TuitionStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Tuition $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}