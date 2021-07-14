<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class LessonEntryMatchesTimetable extends Constraint {

    public $message = 'No matching timetable lesson found.';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}