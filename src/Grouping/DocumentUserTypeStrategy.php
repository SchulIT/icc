<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;

class DocumentUserTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param Document $document
     * @return UserType[]
     */
    public function computeKey($document, array $options = [ ]) {
        return array_map(function(UserTypeEntity $visibility) {
            return $visibility->getUserType();
        }, $document->getVisibilities()->toArray());
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param UserType $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new DocumentUserTypeGroup($key);
    }
}