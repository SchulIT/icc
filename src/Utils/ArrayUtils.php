<?php

namespace App\Utils;

class ArrayUtils {

    public static function apply(array &$items, \Closure $closure): void {
        foreach($items as $item) {
            $closure($item);
        }
    }

    public static function createArrayWithKeys(array $items, \Closure $keyFunc): array {
        $array = [ ];

        foreach($items as $item) {
            $key = $keyFunc($item);
            $array[$key] = $item;
        }

        return $array;
    }
}