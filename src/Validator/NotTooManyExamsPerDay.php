<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class NotTooManyExamsPerDay extends Constraint {
    public $message = 'Only {{ maxNumber }} exam(s) per day are allowed. Got {{ number }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}