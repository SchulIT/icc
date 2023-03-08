<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Settings\UntisSettings;

class StudentIdGenerator {

    /**
     * @param iterable|StudentIdGeneratorInterface[] $generators
     */
    public function __construct(private readonly iterable $generators, private readonly UntisSettings $settings) { }

    public function generate(Student $student): ?string {
        foreach($this->generators as $generator) {
            if($generator->supports($this->settings->getStudentIdentifierFormat())) {
                return $generator->generate($student);
            }
        }

        return null;
    }
}