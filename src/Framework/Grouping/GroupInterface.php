<?php

namespace App\Framework\Grouping;

/**
 * @template TKey
 * @template TValue
 */
interface GroupInterface {

    /**
     * @return TKey
     */
    public function getKey(): mixed;

    /**
     * @param TValue $item
     */
    public function addItem($item): void;
}