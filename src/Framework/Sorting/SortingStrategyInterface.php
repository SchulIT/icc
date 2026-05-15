<?php

namespace App\Framework\Sorting;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.sorting_strategy')]
interface SortingStrategyInterface {

    public function compare($objectA, $objectB): int;
}