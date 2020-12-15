<?php

namespace App\Converter;

use App\Entity\Student;

class StudentStringConverter {
    public function convert(Student $student, bool $includeGrade = false) {
        if($includeGrade === true) {
            return sprintf('%s, %s [%s]', $student->getLastname(), $student->getFirstname(), $student->getGrade()->getName());
        }

        return sprintf('%s, %s', $student->getLastname(), $student->getFirstname());
    }
}