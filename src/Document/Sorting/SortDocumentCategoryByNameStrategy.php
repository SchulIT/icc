<?php

namespace App\Document\Sorting;

use App\Document\Entity\DocumentCategory;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class SortDocumentCategoryByNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param DocumentCategory $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}