<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateIsNotInPast extends Constraint {
    public $message = 'This value must not be in the past.';
}