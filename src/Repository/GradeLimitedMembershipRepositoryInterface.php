<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeLimitedMembership;
use App\Entity\Section;
use App\Entity\Student;

interface GradeLimitedMembershipRepositoryInterface {

    /**
     * @param Grade $grade
     * @param Section $section
     * @return GradeLimitedMembership[]
     */
    public function findAllByGradeAndSection(Grade $grade, Section $section): array;

    /**
     * @param Student $student
     * @param Section $section
     * @return GradeLimitedMembership[]
     */
    public function findAllByStudentAndSection(Student $student, Section $section): array;

}