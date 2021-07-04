<?php

namespace App\Converter;

use App\Entity\Section;
use App\Entity\Student;
use App\Section\SectionResolver;

class StudentStringConverter {

    private $sectionResolver;

    public function __construct(SectionResolver $sectionResolver) {
        $this->sectionResolver = $sectionResolver;
    }

    public function convert(Student $student, bool $includeGrade = false, ?Section $section = null) {
        if($includeGrade === true) {
            if($section === null) {
                $section = $this->sectionResolver->getCurrentSection();
            }
            return sprintf('%s, %s [%s]', $student->getLastname(), $student->getFirstname(), $student->getGrade($section)->getName());
        }

        return sprintf('%s, %s', $student->getLastname(), $student->getFirstname());
    }
}