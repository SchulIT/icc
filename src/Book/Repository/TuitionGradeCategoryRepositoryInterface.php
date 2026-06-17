<?php

namespace App\Book\Repository;

use App\Book\Entity\TuitionGradeCatalog;
use App\Book\Entity\TuitionGradeCategory;

interface TuitionGradeCategoryRepositoryInterface {

    public function findOneByUuid(string $uuid): ?TuitionGradeCategory;

    /**
     * @return TuitionGradeCategory[]
     */
    public function findAll(): array;

    /**
     * @param TuitionGradeCatalog $catalog
     * @return TuitionGradeCategory[]
     */
    public function findAllByGradeType(TuitionGradeCatalog $catalog): array;

    public function persist(TuitionGradeCategory $category): void;

    public function remove(TuitionGradeCategory $category): void;
}