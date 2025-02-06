<?php

namespace App\Sorting;

class StringStrategy implements SortingStrategyInterface {

    /**
     * DIN5007, see https://de.wikipedia.org/wiki/Alphabetische_Sortierung#Deutschland
     */
    private const TransliterationMap = [
        'ä' => 'ae',
        'Ä' => 'Ae',
        'ü' => 'ue',
        'Ü' => 'Ue',
        'ö' => 'oe',
        'Ö' => 'Oe',
        'ß' => 'ss'
    ];

    private function transliterate(string $string): string {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }

    public function compare($objectA, $objectB): int {
        $objectA = str_replace(
            array_keys(self::TransliterationMap),
            array_values(self::TransliterationMap),
            $this->transliterate((string)$objectA)
        );

        $objectB = str_replace(
            array_keys(self::TransliterationMap),
            array_values(self::TransliterationMap),
            $this->transliterate((string)$objectB)
        );

        return strnatcasecmp($objectA, $objectB);
    }
}