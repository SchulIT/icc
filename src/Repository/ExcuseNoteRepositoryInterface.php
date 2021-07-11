<?php

namespace App\Repository;

use App\Entity\ExcuseNote;
use App\Entity\Student;

interface ExcuseNoteRepositoryInterface {

    /**
     * @param Student $student
     * @return ExcuseNote[]
     */
    public function findByStudent(Student $student): array;

    public function persist(ExcuseNote $note): void;

    public function remove(ExcuseNote $note): void;
}