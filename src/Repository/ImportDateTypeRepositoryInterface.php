<?php

namespace App\Repository;

use App\Entity\ImportDateTime;

interface ImportDateTypeRepositoryInterface {
    public function findAll(): array;

    public function findOneByEntityClass(string $className): ?ImportDateTime;

    public function persist(ImportDateTime $dateTime): void;
}