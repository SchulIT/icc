<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CsrfToken extends Constraint {
    public string $id;

    public string $message = 'The CSRF token is invalid.';
}