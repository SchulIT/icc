<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class NotTooManyExamsPerDay extends Constraint {
    public string $message = 'Only {{ maxNumber }} exam(s) per day are allowed. Got {{ number }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}