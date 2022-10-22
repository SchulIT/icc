<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\DocumentCategory;

class DocumentCategoryStrategy implements GroupingStrategyInterface {

    /**
     * @param Document $object
     * @return DocumentCategory
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getCategory();
    }

    /**
     * @param DocumentCategory $keyA
     * @param DocumentCategory $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param DocumentCategory $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new DocumentCategoryGroup($key);
    }
}