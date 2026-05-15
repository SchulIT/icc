<?php

namespace App\Display;

use App\Common\Entity\Grade;

class GradeGroup extends AbstractGroup {
    public function __construct(private Grade $grade)
    {
    }

    public function getHeader(): string {
        return $this->grade->getName();
    }
}