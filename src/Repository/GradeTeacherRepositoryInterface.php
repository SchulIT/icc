<?php

namespace App\Repository;

use App\Entity\GradeTeacher;

interface GradeTeacherRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return GradeTeacher[]
     */
    public function findAll();

    public function persist(GradeTeacher $gradeTeacher): void;

    public function removeAll(): void;
}