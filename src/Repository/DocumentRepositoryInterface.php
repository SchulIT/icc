<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;

interface DocumentRepositoryInterface {

    /**
     * @param int $id
     * @return Document|null
     */
    public function findOneById(int $id): ?Document;

    /**
     * @param string $uuid
     * @return Document|null
     */
    public function findOneByUuid(string $uuid): ?Document;

    /**
     * @param DocumentCategory $category
     * @return Document[]
     */
    public function findAllByCategory(DocumentCategory $category);

    /**
     * @param User $user
     * @return Document[]
     */
    public function findAllByAuthor(User $user): array;

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