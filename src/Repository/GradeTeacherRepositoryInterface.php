<?php

namespace App\Repository;

use App\Entity\GradeTeacher;
use App\Entity\Section;

interface GradeTeacherRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return GradeTeacher[]
     */
    public function findAll();

    public function persist(GradeTeacher $gradeTeacher): void;

    public function removeAll(Section $section): void;
}