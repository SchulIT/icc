<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Color extends Constraint {
    public $message = 'The value must be a valid HTML encoded color.';
}