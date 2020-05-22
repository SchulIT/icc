<?php

namespace App\View\Filter;

use App\Entity\User;
use App\Sorting\GradeNameStrategy;

class GradeFilter extends AbstractGradeFilter {

    public function handle(?string $gradeUuid, User $user) {
        $grades = $this->getGrades($user, $defaultGrade);

        $grade = $gradeUuid !== null ?
            $grades[$gradeUuid] ?? $defaultGrade : $defaultGrade;

        $this->sorter->sort($grades, GradeNameStrategy::class);

        return new GradeFilterView($grades, $grade);
    }
}