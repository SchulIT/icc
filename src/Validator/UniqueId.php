<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueId extends Constraint {
    /**
     * @var string
     */
    public string $propertyPath;

    public string $message = 'Id {{ id }} is used more than once. All ids must be unique.';
}