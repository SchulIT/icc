<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CsrfToken extends Constraint {
    public string $id;

    public string $message = 'The CSRF token is invalid.';

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, ?string $id = null) {
        parent::__construct($options, $groups, $payload);

        if($id !== null) {
            $this->id = $id;
        }
    }
}