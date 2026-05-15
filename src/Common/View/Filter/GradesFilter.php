<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\User;
use App\Common\Sorting\GradeNameStrategy;
use App\Common\View\Filter\AbstractGradeFilter;
use App\Common\View\Filter\GradesFilterView;

class GradesFilter extends AbstractGradeFilter {
    
    public function handle(array $gradeUuids, ?Section $section, User $user): GradesFilterView {
        $grades = $this->getGrades($user, $section, $defaultGrade);
        $selectedGrades = [ ];

        foreach($grades as $grade) {
            if(in_array((string)$grade->getUuid(), $gradeUuids)) {
                $selectedGrades[] = $grade;
            }
        }

        $this->sorter->sort($grades, GradeNameStrategy::class);

        return new GradesFilterView($grades, $selectedGrades);
    }
}