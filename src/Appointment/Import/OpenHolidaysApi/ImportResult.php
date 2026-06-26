<?php

namespace App\Appointment\Import\OpenHolidaysApi;

class ImportResult {
    public function __construct(
        public int $added,
        public int $updated
    ) {

    }

    public static function fromZero(): ImportResult {
        return new self(0, 0);
    }
}
