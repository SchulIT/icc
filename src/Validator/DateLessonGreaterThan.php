<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateLessonGreaterThan extends Constraint {
    public $message = 'This value should be greater than or equal to {{ compared_value }}.';

    public $propertyPath;
}