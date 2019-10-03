<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\StudyGroup;
use App\Entity\UserType;

interface DocumentRepositoryInterface {

    /**
     * @param int $id
     * @return Document|null
     */
    public function findOneById(int $id): ?Document;

    /**
     * @param DocumentCategory $category
     * @return Document[]
     */
    public function findAllByCategory(DocumentCategory $category);

    /**
     * @param UserType $type
     * @param StudyGroup|null $studyGroup
     * @param string|null $q
     * @return Document[]
     */
    public function findAllFor(UserType $type, ?StudyGroup $studyGroup = null, ?string $q = null);

    /**
     * @return Document[]
     */
    public function findAll();

    /**
     * @param Document $document
     */
    public function persist(Document $document): void;

    /**
     * @param Document $document
     */
    public function remove(Document $document): void;
}