<?php

namespace App\Utils;

use InvalidArgumentException;
use Closure;

class ArrayUtils {

    public static function apply(iterable &$items, Closure $closure): void {
        foreach($items as $item) {
            $closure($item);
        }
    }

    public static function first(iterable $items, Closure $predicate) {
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
            throw new InvalidArgumentException('$keys and $items parameter need to have the same length.');
        }

        for($i = 0; $i < $count; $i++) {
            $array[$keys[$i]] = $values[$i];
        }

        return $array;
    }

    public static function createArrayWithKeys(iterable $items, Closure $keyFunc, bool $multiValue = false): array {
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

    public static function createArrayWithKeysAndValues(iterable $items, Closure $keyFunc, Closure $valueFunc): array {
        $array = [ ];

        foreach($items as $item) {
            $array[$keyFunc($item)] = $valueFunc($item);
        }

        return $array;
    }

    public static function findAllWithKeys(iterable $items, array $keys): array {
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
     */
    public static function filterByType(iterable $items, string $type): array {
        $result = [ ];

        foreach($items as $item) {
            if(is_object($item) && $item::class === $type) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public static function unique(iterable $items) {
        $result = [ ];

        foreach($items as $item) {
            if(!in_array($item, $result, true)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public static function iterableToArray(iterable $items): array {
        if(is_array($items)) {
            return $items;
        }

        $array = [ ];

        foreach($items as $item) {
            $array[] = $item;
        }

        return $array;
    }

    public static function areEqual(iterable $iterableA, iterable $iterableB) {
        $arrayA = self::iterableToArray($iterableA);
        $arrayB = self::iterableToArray($iterableB);

        if(count($arrayA) != count($arrayB)) {
            return false;
        }

        return count(array_intersect($arrayA, $arrayB)) === count($arrayA);
    }

    /**
     * Like array_intersect but compares using the === operator (and is thus capable of intersecting arrays of objects).
     */
    public static function intersect(iterable $iterableA, iterable $iterableB): array {
        return array_uintersect(
            self::iterableToArray($iterableA),
            self::iterableToArray($iterableB),
            fn($objectA, $objectB) => $objectA === $objectB ? 0 : 1
        );
    }

    /**
     * @return string[]
     */
    public static function toString(iterable $items): array {
        $result = [ ];

        foreach($items as $item) {
            $result[] = (string)$item;
        }

        return $result;
    }

    public static function inArray(mixed $needle, iterable $haystack): bool {
        return in_array($needle, self::iterableToArray($haystack), true);
    }

    public static function remove(iterable $original, iterable $remove) {
        $result = [ ];

        foreach($original as $enum) {
            foreach($remove as $excludeEnum) {
                if($enum === $excludeEnum) {
                    // exclude $enum from result
                    continue 2;
                }
            }

            $result[] = $enum;
        }

        return $result;
    }
}