<?php

namespace App\Framework\Converter;

use DateTime;

class TimestampDateTimeConverter {
    public function convert(int $timestamp): DateTime {
        return (new DateTime())
            ->setTimestamp($timestamp);
    }
}