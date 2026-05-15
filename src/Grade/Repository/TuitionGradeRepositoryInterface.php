<?php

namespace App\Grade\Repository;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use App\Grade\Entity\TuitionGrade;
use App\Grade\Entity\TuitionGradeCategory;
use App\Framework\Repository\TransactionalRepositoryInterface;

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