<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class NotTooManyExamsPerWeek extends Constraint {
    public $message = 'Only {{ maxNumber }} exam(s) per week are allowed. Got {{ number }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}