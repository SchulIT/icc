<?php

namespace App\Framework\Grouping;

use App\Framework\Grouping\GroupInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @template TKey
 * @template T
 */
#[AutoconfigureTag('app.grouping_strategy')]
interface GroupingStrategyInterface {

    /**
     * @param T $object
     * @return TKey
     */
    public function computeKey($object, array $options = [ ]);

    /**
     * @param T $keyA
     * @param T $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool;

    /**
     * @param TKey $key
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface;
}