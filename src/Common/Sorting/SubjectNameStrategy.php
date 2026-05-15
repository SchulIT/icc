<?php

namespace App\Common\Sorting;

use App\Common\Entity\Subject;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class SubjectNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Subject $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}