<?php

namespace App\TeacherAbsence\Repository;

use App\TeacherAbsence\Entity\TeacherAbsenceType;

interface TeacherAbsenceTypeRepositoryInterface {
    public function findAll(): array;

    public function persist(TeacherAbsenceType $type): void;

    public function remove(TeacherAbsenceType $type): void;
}