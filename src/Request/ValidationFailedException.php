<?php

namespace App\Request;

use App\Import\ImportException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends ImportException {

    /** @var ConstraintViolationListInterface */
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->violations = $violations;
    }

    public function getViolations(): ConstraintViolationListInterface {
        return $this->violations;
    }
}