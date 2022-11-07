<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
class DateLessonNotInPast extends Constraint {
    public string $message = 'This value must not be in the past.';

    /** @var array Specifies roles to which this constraint is not applied */
    public array $exceptions = [ ];

    /** @var string|null If set, the validator can ignore this constraint in case the value was not changed. */
    public ?string $propertyName = null;

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, array $exceptions = [ ], ?string $propertyName = null) {
        parent::__construct($options, $groups, $payload);

        if(empty($this->exceptions)) {
            $this->exceptions = $exceptions;
        }

        if(empty($this->propertyName)) {
            $this->propertyName = $propertyName;
        }
    }

}