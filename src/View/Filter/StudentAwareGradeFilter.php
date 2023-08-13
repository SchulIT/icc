<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Student;

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