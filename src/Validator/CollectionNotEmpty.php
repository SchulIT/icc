<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CollectionNotEmpty extends Constraint {

    /** @var string $propertyPath Property path of the target userType. */
    public string $propertyPath;

    public string $message = 'This collection must not be empty.';

    public function __construct(mixed $options = null, array|null $groups = null, mixed $payload = null, ?string $propertyPath = null) {
        parent::__construct($options, $groups, $payload);

        if($propertyPath !== null) {
            $this->propertyPath = $propertyPath;
        }
    }

}