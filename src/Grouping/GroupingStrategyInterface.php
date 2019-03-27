<?php

namespace App\Grouping;

interface GroupingStrategyInterface {

    /**
     * @param mixed $object
     * @return mixed
     */
    public function computeKey($object);

    /**
     * @param mixed $keyA
     * @param mixed $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool;

    /**
     * @param mixed $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface;
}