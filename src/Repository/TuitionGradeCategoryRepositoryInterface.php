<?php

namespace App\Repository;

use App\Entity\TuitionGradeCategory;
use App\Entity\TuitionGradeCatalog;

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