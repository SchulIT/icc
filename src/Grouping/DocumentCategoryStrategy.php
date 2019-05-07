<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\DocumentCategory;

class DocumentCategoryStrategy implements GroupingStrategyInterface {

    /**
     * @param Document $object
     * @return DocumentCategory
     */
    public function computeKey($object) {
        return $object->getCategory();
    }

    /**
     * @param DocumentCategory $keyA
     * @param DocumentCategory $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param DocumentCategory $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new DocumentCategoryGroup($key);
    }
}