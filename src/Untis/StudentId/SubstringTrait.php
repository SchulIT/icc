<?php

namespace App\Untis\StudentId;

trait SubstringTrait {
    private function substring(string $string, ?int $numberOfLetters): string {
        if($numberOfLetters === null || $numberOfLetters === 0) {
            return $string;
        }

        return mb_substr($string, 0, $numberOfLetters);
    }
}