<?php

namespace App\Framework\Sorting;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @template T
 */
#[AutoconfigureTag('app.sorting_strategy')]
interface SortingStrategyInterface {

    /**
     * @param T $objectA
     * @param T $objectB
     * @return int
     */
    public function compare(mixed $objectA, mixed $objectB): int;
}