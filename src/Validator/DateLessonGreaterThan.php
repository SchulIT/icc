<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
class DateLessonGreaterThan extends Constraint {
    public string $message = 'This value should be greater than or equal to {{ compared_value }}.';

    public string $propertyPath;

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, string $propertyPath = '') {
        parent::__construct($options, $groups, $payload);

        if(empty($this->propertyPath)) {
            $this->propertyPath = $propertyPath;
        }
    }
}