<?php

namespace App\Common\Repository;

use App\Common\Entity\TeacherTag;

interface TeacherTagRepositoryInterface {

    public function findAll(): array;

    public function persist(TeacherTag $tag): void;

    public function remove(TeacherTag $tag): void;
}