<?php

namespace App\Request;

use App\Import\ImportException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends ImportException {

    public function __construct(private readonly ConstraintViolationListInterface $violations, $message = "", $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getViolations(): ConstraintViolationListInterface {
        return $this->violations;
    }
}