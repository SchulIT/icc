<?php

namespace App\Import;

use DateTime;
use Exception;
use Throwable;

class SectionNotResolvableException extends Exception {
    public function __construct(DateTime $dateTime, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('Kein Abschnitt gefunden, der zum Datum %s passt.', $dateTime->format('Y-m-d')), $code, $previous);
    }
}