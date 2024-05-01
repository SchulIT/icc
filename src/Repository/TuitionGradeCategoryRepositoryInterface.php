<?php

namespace App\Repository;

use App\Entity\TuitionGradeCategory;
use App\Entity\TuitionGradeCatalog;

interface TuitionGradeCategoryRepositoryInterface {

    /**
     * @return TuitionGradeCategory[]
     */
    public function findAll(): array;

    /**
     * @param TuitionGradeCatalog $type
     * @return TuitionGradeCategory[]
     */
    public function findAllByGradeType(TuitionGradeCatalog $type): array;

    public function persist(TuitionGradeCategory $category): void;

    public function remove(TuitionGradeCategory $category): void;
}