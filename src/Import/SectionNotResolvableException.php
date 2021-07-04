<?php

namespace App\Import;

use DateTime;
use Exception;
use Throwable;

class SectionNotResolvableException extends Exception {
    public function __construct(DateTime $dateTime, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('No section is associated with date %s', $dateTime->format('Y-m-d')), $code, $previous);
    }
}