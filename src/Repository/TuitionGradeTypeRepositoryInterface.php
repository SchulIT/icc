<?php

namespace App\Repository;

use App\Entity\TuitionGradeType;

interface TuitionGradeTypeRepositoryInterface {

    /**
     * @return TuitionGradeType[]
     */
    public function findAll(): array;

    public function persist(TuitionGradeType $type): void;

    public function remove(TuitionGradeType $type): void;
}