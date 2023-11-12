<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class NotTooManyExamsPerDay extends Constraint {
    public string $message = 'Only {{ maxNumber }} exam(s) per day are allowed. Got {{ number }} for {{ student }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}