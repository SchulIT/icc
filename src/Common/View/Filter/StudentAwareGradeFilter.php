<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\View\Filter\GradeFilterView;

class StudentAwareGradeFilter {
    public function handle(?string $gradeUuid, ?Section $section, ?Student $student): GradeFilterView {
        if($section === null || $student === null) {
            return new GradeFilterView([], null, []);
        }

        $grade = $student->getGrade($section);
        $grades = [ ];

        if($grade !== null) {
            $grades[] = $grade;
            if($grade->getUuid()->toString() !== $gradeUuid) {
                $grade = null;
            }
        }

        return new GradeFilterView($grades, $grade, []);
    }
}