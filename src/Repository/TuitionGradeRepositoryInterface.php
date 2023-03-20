<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;

interface TuitionGradeRepositoryInterface extends TransactionalRepositoryInterface {
    public function findAllByTuition(Tuition $tuition): array;

    public function findAllByStudent(Student $student, Section $section): array;

    public function persist(TuitionGrade $grade): void;
}