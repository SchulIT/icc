<?php

namespace App\LearningManagementSystem\Sorting;

use App\Framework\Sorting\AbstractStringPropertyStrategy;
use App\LearningManagementSystem\Entity\LearningManagementSystem;

class LearningManagementSystemNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param LearningManagementSystem $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}
