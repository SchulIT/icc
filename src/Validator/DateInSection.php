<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DateInSection extends Constraint {
    public string $message = 'This date must be between {{ start }} and {{ end }}.';

    public string $propertyPath;

    public function __construct(string $propertyPath, mixed $options = null, array|null $groups = null, mixed $payload = null) {
        parent::__construct($options, $groups, $payload);

        $this->propertyPath = $propertyPath;
    }
}