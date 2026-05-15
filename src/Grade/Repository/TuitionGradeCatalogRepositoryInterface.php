<?php

namespace App\Grade\Repository;

use App\Grade\Entity\TuitionGradeCatalog;

interface TuitionGradeCatalogRepositoryInterface {

    /**
     * @return TuitionGradeCatalog[]
     */
    public function findAll(): array;

    public function persist(TuitionGradeCatalog $type): void;

    public function remove(TuitionGradeCatalog $type): void;
}