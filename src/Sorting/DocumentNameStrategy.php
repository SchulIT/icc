<?php

namespace App\Sorting;

use App\Entity\Document;

class DocumentNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Document $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}