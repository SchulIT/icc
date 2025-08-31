<?php

namespace App\Sorting;

use App\Entity\LearningManagementSystem;

class LearningManagementSystemNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param LearningManagementSystem $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}
