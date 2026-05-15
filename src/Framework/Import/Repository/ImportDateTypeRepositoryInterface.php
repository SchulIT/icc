<?php

namespace App\Framework\Import\Repository;

use App\Framework\Import\Entity\ImportDateTime;

interface ImportDateTypeRepositoryInterface {
    public function findAll(): array;

    public function findOneByEntityClass(string $className): ?ImportDateTime;

    public function persist(ImportDateTime $dateTime): void;
}