<?php

namespace App\Untis\StudentId;

use App\Entity\Student;
use App\Settings\UntisSettings;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class StudentIdGenerator {

    /**
     * @param iterable|StudentIdGeneratorInterface[] $generators
     */
    public function __construct(#[AutowireIterator('app.untis.student_id_generator')] private iterable $generators, private UntisSettings $settings) { }

    public function generate(Student $student): ?string {
        foreach($this->generators as $generator) {
            if($generator->supports($this->settings->getStudentIdentifierFormat())) {
                return $generator->generate($student);
            }
        }

        return null;
    }
}