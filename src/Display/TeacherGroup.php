<?php

namespace App\Display;

use App\Common\Entity\Teacher;

class TeacherGroup extends AbstractGroup {
    public function __construct(private readonly Teacher $teacher)
    {
    }

    public function getKey(): Teacher {
        return $this->teacher;
    }

    public function getHeader(): string {
        return $this->teacher->getAcronym();
    }
}