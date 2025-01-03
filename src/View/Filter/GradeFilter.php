<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\User;
use App\Sorting\GradeNameStrategy;

class GradeFilter extends AbstractGradeFilter {

    public function handle(?string $gradeUuid, ?Section $section, User $user, bool $setDefaultGrade = false) {
        $grades = $this->getGrades($user, $section, $defaultGrade);

        if($setDefaultGrade === false) {
            $defaultGrade = null;
        }

        $grade = $gradeUuid !== null ?
            $grades[$gradeUuid] ?? $defaultGrade : $defaultGrade;

        $this->sorter->sort($grades, GradeNameStrategy::class);
        $ownGrades = [ ];

        if($user->isTeacher() && $user->getTeacher() !== null && $section !== null) {
            $teacher = $user->getTeacher();
            $ownGrades = $this->getTeachedGradesForTeacher($teacher, $section);
        }

        return new GradeFilterView($grades, $grade, $ownGrades);
    }
}