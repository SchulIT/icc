<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class NotAResource extends Constraint {
    public string $message = 'This room cannot be created as a resource with the same name ({{ name }}) already exists.';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array|string {
        return self::CLASS_CONSTRAINT;
    }
}