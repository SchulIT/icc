<?php

namespace App\Import;

use Throwable;

class SectionNotFoundException extends ImportException {
    public function __construct($section, $year, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('Section %d/%d was not found.', $section, $year), $code, $previous);
    }
}