<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Untis\StudentIdFormat;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.untis.student_id_generator')]
interface StudentIdGeneratorInterface {
    public function supports(StudentIdFormat $format): bool;

    public function generate(Student $student): string;
}