<?php

namespace App\Tools;

use App\Entity\Grade;

class GradeTuitionTeachersIntersection {

    public function __construct(private readonly Grade $leftGrade, private readonly Grade $rightGrade, private readonly array $intersection) {

    }

    /**
     * @return Grade
     */
    public function getLeftGrade(): Grade {
        return $this->leftGrade;
    }

    /**
     * @return Grade
     */
    public function getRightGrade(): Grade {
        return $this->rightGrade;
    }

    /**
     * @return array
     */
    public function getIntersection(): array {
        return $this->intersection;
    }
}