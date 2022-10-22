<?php

namespace App\Display;

use App\Entity\Teacher;

class TeacherGroup extends AbstractGroup {
    public function __construct(private Teacher $teacher)
    {
    }

    public function getHeader(): string {
        return $this->teacher->getAcronym();
    }
}