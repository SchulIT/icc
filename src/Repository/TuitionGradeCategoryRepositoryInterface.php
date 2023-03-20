<?php

namespace App\Repository;

use App\Entity\TuitionGradeCategory;

interface TuitionGradeCategoryRepositoryInterface {

    /**
     * @return TuitionGradeCategory[]
     */
    public function findAll(): array;

    public function persist(TuitionGradeCategory $category): void;

    public function remove(TuitionGradeCategory $category): void;
}