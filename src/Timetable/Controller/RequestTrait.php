<?php

namespace App\Timetable\Controller;

trait RequestTrait {
    protected function getArrayOrNull($input): ?array {
        if ($input === null || is_array($input)) {
            return $input;
        }

        return null;
    }
}