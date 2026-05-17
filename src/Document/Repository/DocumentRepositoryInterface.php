<?php

namespace App\Document\Repository;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

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
     * @param User $user
     * @return int
     */
    public function countDocumentsEditableByAuthor(User $user): int;

    /**
     * @return Document[]
     */
    public function findAllFor(UserType $type, ?Grade $grade = null, ?string $q = null): array;

    /**
     * @return Document[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @param UserType|null $userType
     * @param Grade|null $grade
     * @param DocumentCategory|null $category
     * @param string|null $query
     * @return PaginatedResult<Document>
     */
    public function findPaginated(PaginationQuery $paginationQuery, UserType|null $userType, Grade|null $grade = null, DocumentCategory|null $category = null, string|null $query = null): PaginatedResult;

    /**
     * @param Document $document
     */
    public function persist(Document $document): void;

    /**
     * @param Document $document
     */
    public function remove(Document $document): void;
}