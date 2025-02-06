<?php

namespace App\Sorting;

class StringStrategy implements SortingStrategyInterface {

    private function transliterate(string $string): string {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }

    public function compare($objectA, $objectB): int {
        return strnatcasecmp($this->transliterate((string)$objectA), $this->transliterate((string)$objectB));
    }
}