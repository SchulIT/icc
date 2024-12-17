<?php

namespace App\Book\Grade\Export\Schild;

use Exception;
use Throwable;

class StudentNotFoundException extends Exception {

    public const  NOT_FOUND = 'Kind nicht gefunden';
    public const  AMBIGUOUS = 'Mehrere Kinder gefunden';

    public function __construct(private readonly string $reason, string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getReason(): string {
        return $this->reason;
    }
}