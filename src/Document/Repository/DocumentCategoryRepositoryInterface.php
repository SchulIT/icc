<?php

namespace App\Document\Repository;

use App\Document\Entity\DocumentCategory;

interface DocumentCategoryRepositoryInterface {

    /**
     * @param int $id
     * @return DocumentCategory|null
     */
    public function findOneById(int $id): ?DocumentCategory;

    /**
     * @return DocumentCategory[]
     */
    public function findAll();

    /**
     * @param DocumentCategory $category
     */
    public function persist(DocumentCategory $category): void;

    /**
     * @param DocumentCategory $category
     */
    public function remove(DocumentCategory $category): void;
}