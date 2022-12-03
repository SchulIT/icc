<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Color extends Constraint {
    public string $message = 'Color {{ value }} is not a valid HTML color.';
}