<?php

namespace App\Common\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateInActiveSection extends Constraint {
    public string $message = 'This date must be inside the active section ({{ start }}-{{ end }}).';
}