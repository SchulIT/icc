<?php

namespace App\Common\Repository;

use App\Common\Entity\GradeTeacher;
use App\Common\Entity\Section;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface GradeTeacherRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return GradeTeacher[]
     */
    public function findAll();

    public function persist(GradeTeacher $gradeTeacher): void;

    public function removeAll(Section $section): void;
}