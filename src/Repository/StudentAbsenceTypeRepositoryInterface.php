<?php

namespace App\Repository;

use App\Entity\StudentAbsenceType;

interface StudentAbsenceTypeRepositoryInterface {

    /**
     * @return StudentAbsenceType[]
     */
    public function findAll(): array;

    public function persist(StudentAbsenceType $absenceType): void;

    public function remove(StudentAbsenceType $absenceType): void;
}