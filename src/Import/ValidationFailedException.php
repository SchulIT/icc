<?php

namespace App\Import;

use Throwable;

class ValidationFailedException extends ImportException {
    private $violations;

    public function __construct(array $violations, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->violations = $violations;
    }

    public function getViolations(): array {
        return $this->violations;
    }
}