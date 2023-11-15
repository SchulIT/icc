<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeResponsibility;
use App\Entity\Section;

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