<?php

namespace App\Repository;

use App\Entity\BookIntegrityCheckRun;
use App\Entity\Student;

interface BookIntegrityCheckRunRepositoryInterface {
    public function findByStudent(Student $student): ?BookIntegrityCheckRun;

    public function persist(BookIntegrityCheckRun $run): void;

    public function remove(BookIntegrityCheckRun $run): void;
}