<?php

namespace App\Controller;

trait RequestTrait {
    protected function getArrayOrNull($input): ?array {
        if ($input === null || is_array($input)) {
            return $input;
        }

        return null;
    }
}