<?php

namespace App\Display;

use App\Entity\Grade;

class GradeGroup extends AbstractGroup {
    private $grade;

    public function __construct(Grade $grade) {
        $this->grade = $grade;
    }

    public function getHeader(): string {
        return $this->grade->getName();
    }
}