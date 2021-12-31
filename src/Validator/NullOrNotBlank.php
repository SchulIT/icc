<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NullOrNotBlank extends Constraint {
    public string $message = 'This value should be null or must not be an empty string';
}