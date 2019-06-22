<?php

namespace App\Sorting;

use App\Entity\DocumentCategory;

class DocumentCategoryNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param DocumentCategory $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}