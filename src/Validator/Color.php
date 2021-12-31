<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Color extends Constraint {
    public string $message = 'Color {{ value }} is not a valid HTML color.';
}