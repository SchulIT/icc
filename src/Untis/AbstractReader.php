<?php

namespace App\Untis;

use DateTime;
use League\Csv\Reader;
use ValueError;

abstract class AbstractReader {

    protected function getInt(?string $value): int {
        if($value === null) {
            return 0;
        }

        return intval($value);
    }

    public function getIntOrNull(?string $value): ?int {
        if(empty($value)) {
            return null;
        }

        return intval($value);
    }

    protected function getStringOrNull(?string $value): ?string {
        if(empty($value)) {
            return null;
        }

        return $value;
    }

    /**
     * @return int[]
     */
    protected function getIntArrayOrEmptyArray(?string $value, string $separator = '~'): array {
        $stringArray = $this->getStringArrayOrEmptyArray($value, $separator);

        return array_map(fn($value) => intval($value), $stringArray);
    }

    /**
     * @return string[]
     */
    protected function getStringArrayOrEmptyArray(?string $value, string $separator = '~'): array {
        if(empty($value) === true) {
            return [ ];
        }

        if(empty($separator)) {
            throw new ValueError('Separator must not be empty.');
        }

        return array_map(fn($value) => trim($value), explode($separator, $value));
    }

    protected function convertDate(?string $dateString): ?DateTime {
        if(empty($dateString)) {
            return null;
        }

        $dateTime = DateTime::createFromFormat("Ymd", $dateString);
        $dateTime->setTime(0, 0, 0);
        return $dateTime;
    }

    protected function convertDateTime(?string $dateTimeString): ?DateTime {
        if(empty($dateTimeString)) {
            return null;
        }

        return DateTime::createFromFormat("YmdHi", $dateTimeString);
    }
}