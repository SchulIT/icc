<?php

namespace App\Framework\Sorting;

/**
 * @implements SortingStrategyInterface<string>
 */
class StringStrategy implements SortingStrategyInterface {

    private function transliterate(string $string): string {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }

    public function compare(mixed $objectA, mixed $objectB): int {
        return strnatcasecmp($this->transliterate((string)$objectA), $this->transliterate((string)$objectB));
    }
}