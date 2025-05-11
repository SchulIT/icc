<?php

namespace App\Sorting;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.sorting_strategy')]
interface SortingStrategyInterface {

    public function compare($objectA, $objectB): int;
}