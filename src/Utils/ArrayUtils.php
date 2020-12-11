<?php

namespace App\Utils;

use Closure;

class ArrayUtils {

    public static function apply(array &$items, \Closure $closure): void {
        foreach($items as $item) {
            $closure($item);
        }
    }

    public static function first(array $items, Closure $predicate) {
        foreach($items as $item) {
            if($predicate($item) === true) {
                return $item;
            }
        }

        return null;
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

    public static function createArrayWithKeys(array $items, \Closure $keyFunc, bool $multiValue = false): array {
        $array = [ ];

        foreach($items as $item) {
            $keys = $keyFunc($item);

            if(!is_iterable($keys)) {
                $keys = [ $keys ];
            }

            foreach($keys as $key) {
                if ($multiValue === true) {
                    if (!isset($array[$key])) {
                        $array[$key] = [];
                    }

                    $array[$key][] = $item;
                } else {
                    $array[$key] = $item;
                }
            }
        }

        return $array;
    }

    public static function createArrayWithKeysAndValues(array $items, \Closure $keyFunc, \Closure $valueFunc): array {
        $array = [ ];

        foreach($items as $item) {
            $array[$keyFunc($item)] = $valueFunc($item);
        }

        return $array;
    }

    public static function findAllWithKeys(array $items, array $keys): array {
        $result = [ ];

        foreach($items as $key => $item) {
            if(in_array($key, $keys)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Returns all items of an array of object which are the same type as given.
     *
     * @param array $items
     * @param string $type
     * @return array
     */
    public static function filterByType(array $items, string $type): array {
        $result = [ ];

        foreach($items as $item) {
            if(is_object($item) && get_class($item) === $type) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public static function unique(array $items) {
        $result = [ ];

        foreach($items as $item) {
            if(!in_array($item, $result, true)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}