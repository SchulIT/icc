<?php

namespace App\Display;

use App\Entity\Teacher;

class TeacherGroup extends AbstractGroup {
    /** @var Teacher */
    private $teacher;

    public function __construct(Teacher $teacher) {
        $this->teacher = $teacher;
    }

    public function getHeader(): string {
        return $this->teacher->getAcronym();
    }
}