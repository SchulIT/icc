<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CollectionNotEmpty extends Constraint {

    /** @var string $propertyPath Property path of the target userType. */
    public string $propertyPath;

    public string $message = 'This collection must not be empty.';

}