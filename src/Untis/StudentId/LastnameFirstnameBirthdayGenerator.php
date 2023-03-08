<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Settings\UntisSettings;
use App\Untis\StudentIdFormat;

class LastnameFirstnameBirthdayGenerator implements StudentIdGeneratorInterface {

    use SubstringTrait;

    public function __construct(private readonly UntisSettings $settings) { }

    public function supports(StudentIdFormat $format): bool {
        return $format === StudentIdFormat::LastnameFirstnameBirthday;
    }

    public function generate(Student $student): string {
        $firstname = $this->substring($student->getFirstname(), $this->settings->getStudentIdentifierNumberOfLettersOfFirstname());
        $lastname = $this->substring($student->getLastname(), $this->settings->getStudentIdentifierNumberOfLettersOfLastname());
        $birthday = $student->getBirthday()->format('Ymd');

        return implode($this->settings->getStudentIdentifierSeparator(), [$lastname, $firstname, $birthday]);
    }
}