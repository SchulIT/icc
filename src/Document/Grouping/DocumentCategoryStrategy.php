<?php

namespace App\Document\Grouping;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Document\Grouping\DocumentCategoryGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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