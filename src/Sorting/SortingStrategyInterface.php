<?php

namespace App\Sorting;

interface SortingStrategyInterface {

    public function compare($objectA, $objectB): int;
}