<?php

namespace App\Sorting;

use App\Entity\Document;

class DocumentNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Document $object
     */
    protected function getValue($object): string {
        return $object->getTitle();
    }
}