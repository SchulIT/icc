<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateLessonGreaterThan extends Constraint {
    public string $message = 'This value should be greater than or equal to {{ compared_value }}.';

    public string $propertyPath;

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, string $propertyPath = null) {
        parent::__construct($options, $groups, $payload);

        if(!empty($propertyPath)) {
            $this->propertyPath = $propertyPath;
        }
    }
}