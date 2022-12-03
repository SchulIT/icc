<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class SubsetOf extends AbstractComparison {
    public function __construct(mixed $value = null, string $propertyPath = null, string $message = 'This should be a subset of {{ compared_value_path }}.', array $groups = null, mixed $payload = null, array $options = []) {
        parent::__construct($value, $propertyPath, $message, $groups, $payload, $options);
    }
}