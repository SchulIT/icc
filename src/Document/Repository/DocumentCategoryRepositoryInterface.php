<?php

namespace App\Document\Repository;

use App\Document\Entity\DocumentCategory;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

interface DocumentCategoryRepositoryInterface {

    /**
     * @param int $id
     * @return DocumentCategory|null
     */
    public function findOneById(int $id): ?DocumentCategory;

    /**
     * @return DocumentCategory[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<DocumentCategory>
     */
    public function findPaginated(PaginationQuery $paginationQuery): PaginatedResult;

    /**
     * @param DocumentCategory $category
     */
    public function persist(DocumentCategory $category): void;

    /**
     * @param DocumentCategory $category
     */
    public function remove(DocumentCategory $category): void;
}