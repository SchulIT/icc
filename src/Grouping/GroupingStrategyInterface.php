<?php

namespace App\Grouping;

interface GroupingStrategyInterface {

    /**
     * @param mixed $object
     * @return mixed
     */
    public function computeKey($object, array $options = [ ]);

    /**
     * @param mixed $keyA
     * @param mixed $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool;

    /**
     * @param mixed $key
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface;
}