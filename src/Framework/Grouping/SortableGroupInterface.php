<?php

namespace App\Framework\Grouping;

/**
 * @template TKey
 * @template TValue
 * @extends GroupInterface<TKey, TValue>
 */
interface SortableGroupInterface extends GroupInterface {

    /**
     * @return TValue[]
     */
    public function &getItems(): array;
}