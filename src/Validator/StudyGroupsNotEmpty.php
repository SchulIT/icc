<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StudyGroupsNotEmpty extends Constraint {

    /** @var string $propertyPath Property path of the target userType. */
    public $propertyPath;

    public $message = 'This collection must not be empty.';

}