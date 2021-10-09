<?php

namespace App\Untis;

use DateTime;
use League\Csv\Reader;

abstract class AbstractReader {

    protected function getInt(?string $value): int {
        if($value === null) {
            return 0;
        }

        return intval($value);
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    protected function getStringOrNull(?string $value): ?string {
        if(empty($value)) {
            return null;
        }

        return $value;
    }

    /**
     * @param string|null $value
     * @param string $separator
     * @return int[]
     */
    protected function getIntArrayOrEmptyArray(?string $value, string $separator = '~'): array {
        $stringArray = $this->getStringArrayOrEmptyArray($value, $separator);

        return array_map(function($value) {
            return intval($value);
        }, $stringArray);
    }

    /**
     * @param string|null $value
     * @param string $separator
     * @return string[]
     */
    protected function getStringArrayOrEmptyArray(?string $value, string $separator = '~'): array {
        if(empty($value)) {
            return [ ];
        }

        return array_map(function($value) {
            return trim($value);
        }, explode($separator, $value));
    }

    /**
     * @param string|null $dateString
     * @return DateTime|null
     */
    protected function convertDate(?string $dateString): ?DateTime {
        if(empty($dateString)) {
            return null;
        }

        $dateTime = DateTime::createFromFormat("Ymd", $dateString);
        $dateTime->setTime(0, 0, 0);
        return $dateTime;
    }

    /**
     * @param string|null $dateTimeString
     * @return DateTime|null
     */
    protected function convertDateTime(?string $dateTimeString): ?DateTime {
        if(empty($dateTimeString)) {
            return null;
        }

        return DateTime::createFromFormat("YmdHi", $dateTimeString);
    }
}