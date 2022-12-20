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
        return array_map(fn(UserTypeEntity $visibility) => $visibility->getUserType(), $document->getVisibilities()->toArray());
    }

    /**
     * @param UserType $keyA
     * @param UserType $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param UserType $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new DocumentUserTypeGroup($key);
    }
}