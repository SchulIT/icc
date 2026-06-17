<?php

namespace App\Book\Repository;

use App\Book\Entity\TuitionGradeCatalog;

interface TuitionGradeCatalogRepositoryInterface {

    /**
     * @return TuitionGradeCatalog[]
     */
    public function findAll(): array;

    public function persist(TuitionGradeCatalog $type): void;

    public function remove(TuitionGradeCatalog $type): void;
}