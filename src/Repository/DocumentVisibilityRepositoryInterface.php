<?php

namespace App\Repository;

use App\Entity\DocumentVisibility;

interface DocumentVisibilityRepositoryInterface {

    /**
     * @return DocumentVisibility[]
     */
    public function findAll(): array;

    /**
     * @param DocumentVisibility $documentVisibility
     */
    public function persist(DocumentVisibility $documentVisibility): void;
}