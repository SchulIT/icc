<?php

namespace App\Untis\StudentId;

use App\Common\Entity\Student;
use App\Untis\Settings\UntisSettings;
use App\Untis\StudentIdFormat;

class LastnameFirstnameGenerator implements StudentIdGeneratorInterface {

    use SubstringTrait;

    public function __construct(private readonly UntisSettings $settings) { }

    public function supports(StudentIdFormat $format): bool {
        return $format === StudentIdFormat::LastnameFirstname;
    }

    public function generate(Student $student): string {
        $firstname = $this->substring($student->getFirstname(), $this->settings->getStudentIdentifierNumberOfLettersOfFirstname());
        $lastname = $this->substring($student->getLastname(), $this->settings->getStudentIdentifierNumberOfLettersOfLastname());

        return implode($this->settings->getStudentIdentifierSeparator(), [$lastname, $firstname]);
    }
}