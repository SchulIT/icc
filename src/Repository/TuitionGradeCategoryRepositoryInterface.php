<?php

namespace App\Repository;

use App\Entity\TuitionGradeCategory;
use App\Entity\TuitionGradeType;

interface TuitionGradeCategoryRepositoryInterface {

    /**
     * @return TuitionGradeCategory[]
     */
    public function findAll(): array;

    /**
     * @param TuitionGradeType $type
     * @return TuitionGradeCategory[]
     */
    public function findAllByGradeType(TuitionGradeType $type): array;

    public function persist(TuitionGradeCategory $category): void;

    public function remove(TuitionGradeCategory $category): void;
}