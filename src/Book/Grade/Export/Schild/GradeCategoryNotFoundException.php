<?php

namespace App\Book\Grade\Export\Schild;

use Exception;

class GradeCategoryNotFoundException extends Exception {
    public function __construct(public readonly string $uuid, string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
