<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class NotTooManyExamsPerWeek extends Constraint {
    public string $message = 'Only {{ maxNumber }} exam(s) per week are allowed. Got {{ number }}.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}