<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class TuitionResolvable extends Constraint {
    public string $notMatchedmessage = 'Lesson has no matching tuition (subject: {{ subject }}; teachers: {{ teachers }}; grades: {{ grades }}).';
    public string $ambiguousMessage = 'Ambiguous matching tuition (subject: {{ subject }}; teachers: {{ teachers }}; grades: {{ grades }}).';

    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}