<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueId extends Constraint {
    /**
     * @var string
     */
    public string $propertyPath;

    public string $message = 'Id {{ id }} is used more than once. All ids must be unique.';

    public function __construct(mixed $options = null, array|null $groups = null, mixed $payload = null, ?string $propertyPath = null) {
        parent::__construct($options, $groups, $payload);

        if(!empty($propertyPath)) {
            $this->propertyPath = $propertyPath;
        }
    }
}