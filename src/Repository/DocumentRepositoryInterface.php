<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;

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