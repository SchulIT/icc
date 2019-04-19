<?php

namespace App\Converter;

use App\Entity\Student;

class StudentStringConverter {
    public function convert(Student $student) {
        return sprintf('%s, %s', $student->getLastname(), $student->getFirstname());
    }
}