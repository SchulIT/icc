<?php

namespace App\Book\Repository;

use App\Book\Entity\BookIntegrityCheckRun;
use App\Common\Entity\Student;

interface BookIntegrityCheckRunRepositoryInterface {
    public function findByStudent(Student $student): ?BookIntegrityCheckRun;

    public function persist(BookIntegrityCheckRun $run): void;

    public function remove(BookIntegrityCheckRun $run): void;
}