<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraints\AbstractComparison;

#[Attribute]
class SubsetOf extends AbstractComparison {
    public function __construct(mixed $value = null, string|null $propertyPath = null, string $message = 'This should be a subset of {{ compared_value_path }}.', array|null $groups = null, mixed $payload = null, array $options = []) {
        parent::__construct($value, $propertyPath, $message, $groups, $payload, $options);
    }
}