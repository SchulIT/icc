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

    public function compare($objectA, $objectB): int {
        $objectA = str_replace(
            array_keys(self::TransliterationMap),
            array_values(self::TransliterationMap),
            (string)$objectA
        );

        $objectB = str_replace(
            array_keys(self::TransliterationMap),
            array_values(self::TransliterationMap),
            (string)$objectB
        );

        return strnatcasecmp($objectA, $objectB);
    }
}