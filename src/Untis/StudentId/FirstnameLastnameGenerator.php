<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Settings\UntisSettings;
use App\Untis\StudentIdFormat;

class FirstnameLastnameGenerator implements StudentIdGeneratorInterface {

    use SubstringTrait;

    public function __construct(private readonly UntisSettings $settings) { }

    public function supports(StudentIdFormat $format): bool {
        return $format === StudentIdFormat::FirstnameLastname;
    }

    public function generate(Student $student): string {
        $firstname = $this->substring($student->getFirstname(), $this->settings->getStudentIdentifierNumberOfLettersOfFirstname());
        $lastname = $this->substring($student->getLastname(), $this->settings->getStudentIdentifierNumberOfLettersOfLastname());

        return implode($this->settings->getStudentIdentifierSeparator(), [$firstname, $lastname]);
    }
}