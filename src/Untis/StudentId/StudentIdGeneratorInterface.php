<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Untis\StudentIdFormat;

interface StudentIdGeneratorInterface {
    public function supports(StudentIdFormat $format): bool;

    public function generate(Student $student): string;
}