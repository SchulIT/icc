<?php

namespace App\Sorting;

use App\Entity\Subject;

class SubjectNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Subject $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}