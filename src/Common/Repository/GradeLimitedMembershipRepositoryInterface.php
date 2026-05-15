<?php

namespace App\Common\Repository;

use App\Common\Entity\Grade;
use App\Common\Entity\GradeLimitedMembership;
use App\Common\Entity\Section;
use App\Common\Entity\Student;

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