<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateIsNotInPast extends Constraint {
    public string $message = 'This value must not be in the past.';
}