<?php

namespace App\Book\Repository;

use App\Common\Entity\Grade;
use App\Book\Entity\GradeResponsibility;
use App\Common\Entity\Section;

interface GradeResponsibilityRepositoryInterface {

    /**
     * @param Grade $grade
     * @param Section $section
     * @return GradeResponsibility[]
     */
    public function findAllByGrade(Grade $grade, Section $section): array;

    public function persist(GradeResponsibility $responsibility): void;

    public function remove(GradeResponsibility $responsibility): void;
}