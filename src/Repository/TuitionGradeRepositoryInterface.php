<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;
use App\Entity\TuitionGradeCategory;

interface TuitionGradeRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param Tuition $tuition
     * @return TuitionGrade[]
     */
    public function findAllByTuition(Tuition $tuition): array;

    /**
     * @param Student $student
     * @param Section $section
     * @return TuitionGrade[]
     */
    public function findAllByStudent(Student $student, Section $section): array;

    public function countByTuitionGradeCategory(TuitionGradeCategory $category): int;

    public function persist(TuitionGrade $grade): void;

    public function removeForSection(Section $section): int;
}