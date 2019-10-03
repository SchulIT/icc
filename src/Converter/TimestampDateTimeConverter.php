<?php

namespace App\Converter;

class TimestampDateTimeConverter {
    public function convert(int $timestamp) {
        return (new \DateTime())
            ->setTimestamp($timestamp);
    }
}