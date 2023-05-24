<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateLessonInSection extends Constraint {
    public string $message = 'This date must be inside a section ({{ sections }}).';
}