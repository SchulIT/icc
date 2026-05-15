<?php

namespace App\Document\Repository;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\User;
use App\Common\Entity\UserType;

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
     * @return Document[]
     */
    public function findAllByCategory(DocumentCategory $category);

    /**
     * @param User $user
     * @return Document[]
     */
    public function findAllByAuthor(User $user): array;

    /**
     * @return Document[]
     */
    public function findAllFor(UserType $type, ?Grade $grade = null, ?string $q = null);

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