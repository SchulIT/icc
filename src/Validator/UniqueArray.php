<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueArray extends Constraint {
    public string $message = 'At least one choice is selected more than once.';
}