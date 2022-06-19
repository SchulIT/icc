<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class NotTooManyExamsPerWeek extends Constraint {
    public string $message = 'Only {{ maxNumber }} exam(s) per week are allowed. Got {{ number }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}