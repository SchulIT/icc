<?php

namespace App\Common\Converter;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Section\SectionResolverInterface;

class StudentStringConverter {

    public function __construct(private SectionResolverInterface $sectionResolver)
    {
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