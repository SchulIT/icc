<?php

namespace App\Appointment\Recurring;

readonly class Result {
    public function __construct(
        public int $added,
        public int $updated
    ) {

    }
}

