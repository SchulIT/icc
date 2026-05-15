<?php

namespace App\Document\Sorting;

use App\Document\Entity\Document;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class DocumentNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Document $object
     */
    protected function getValue($object): string {
        return $object->getTitle();
    }
}