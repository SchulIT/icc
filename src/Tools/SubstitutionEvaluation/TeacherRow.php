<?php

namespace App\Tools\SubstitutionEvaluation;

use App\Entity\Teacher;

class TeacherRow {
    public function __construct(public readonly Teacher $teacher, public int $numSubstitute = 0, public int $numWasSubstituted = 0) {

    }
}