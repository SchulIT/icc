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
    public $propertyPath;

    public $message = 'Id {{ id }} is used more than once. All ids must be unique.';
}