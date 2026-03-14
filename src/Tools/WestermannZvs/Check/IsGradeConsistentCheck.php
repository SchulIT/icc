<?php

namespace App\Tools\WestermannZvs\Check;

use App\Section\SectionResolverInterface;
use App\Tools\WestermannZvs\StudentMatch;
use Override;

readonly class IsGradeConsistentCheck implements CheckInterface {

    public function __construct(
        private SectionResolverInterface $sectionResolver
    ) {

    }

    #[Override]
    public function needAction(StudentMatch $match): Action|null {
        if($match->schueler === null || $match->student === null) {
            return null;
        }

        $currentSection = $this->sectionResolver->getCurrentSection();

        if($currentSection === null) {
            return null;
        }

        $zsvGrade = '' . $match->schueler->stufe;
        $actualGrade = $match->student->getGrade($currentSection);

        if(empty($zsvGrade) || empty($actualGrade)) {
            return new Action('check.westermann_zsv.grade_inconsistent', [
                '%westermann%' => $zsvGrade,
                '%grade%' => $actualGrade?->getName()
            ]);
        }

        // probably NRW-only
        $replaceMap = [
            11 => 'EF',
            12 => 'Q1',
            13 => 'Q2'
        ];

        foreach($replaceMap as $search => $replace) {
            $zsvGrade = str_replace($search, $replace, $zsvGrade);
        }

        if(!str_contains($actualGrade->getName(), $zsvGrade)) {
            return new Action('check.westermann_zsv.grade_inconsistent', [
                '%westermann%' => $zsvGrade,
                '%grade%' => $actualGrade?->getName()
            ]);
        }

        return null;
    }
}