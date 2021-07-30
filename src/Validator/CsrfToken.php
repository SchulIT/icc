<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CsrfToken extends Constraint {
    public $id;

    public $message = 'The CSRF token is invalid.';
}