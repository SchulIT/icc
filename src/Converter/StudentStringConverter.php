<?php

namespace App\Converter;

use App\Entity\Section;
use App\Entity\Student;
use App\Section\SectionResolverInterface;

class StudentStringConverter {

    private SectionResolverInterface $sectionResolver;

    public function __construct(SectionResolverInterface $sectionResolver) {
        $this->sectionResolver = $sectionResolver;
    }

    public function convert(Student $student, bool $includeGrade = false, ?Section $section = null): string {
        if($includeGrade === true) {
            if($section === null) {
                $section = $this->sectionResolver->getCurrentSection();
            }

            $grade = $student->getGrade($section);

            if($grade !== null) {
                return sprintf('%s, %s [%s]', $student->getLastname(), $student->getFirstname(), $student->getGrade($section)->getName());
            }
        }

        return sprintf('%s, %s', $student->getLastname(), $student->getFirstname());
    }
}