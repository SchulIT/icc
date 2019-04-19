<?php

namespace App\Utils;

class ArrayUtils {

    public static function apply(array &$items, \Closure $closure): void {
        foreach($items as $item) {
            $closure($item);
        }
    }

    public static function createArray(array $keys, array $values) {
        $array = [ ];
        $count = count($keys);

        $keys = array_values($keys);
        $values = array_values($values);

        if(count($keys) !== count($values)) {
            throw new \InvalidArgumentException('$keys and $items parameter need to have the same length.');
        }

        for($i = 0; $i < $count; $i++) {
            $array[$keys[$i]] = $values[$i];
        }

        return $array;
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