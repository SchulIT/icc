<?php

namespace App\Framework\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

abstract class AbstractStringPropertyStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    abstract  protected function getValue($object): string;

    public final function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare(
            $this->getValue($objectA),
            $this->getValue($objectB)
        );
    }
}