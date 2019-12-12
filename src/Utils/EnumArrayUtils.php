<?php

namespace App\Utils;

use MyCLabs\Enum\Enum;

class EnumArrayUtils {

    /**
     * Removes items in $exclude from $original array
     *
     * @param Enum[] $original
     * @param Enum[] $exclude
     * @return Enum[]
     */
    public static function remove(iterable $original, iterable $exclude) {
        $result = [ ];

        foreach($original as $enum) {
            foreach($exclude as $excludeEnum) {
                if($enum->equals($excludeEnum)) {
                    // exclude $enum from result
                    continue 2;
                }
            }

            $result[] = $enum;
        }

        return $result;
    }

    /**
     * @param Enum $needle
     * @param Enum[] $array
     * @return bool
     */
    public static function inArray(Enum $needle, iterable $array): bool {
        foreach($array as $item) {
            if($item->equals($needle)) {
                return true;
            }
        }

        return false;
    }
}