<?php

namespace App\Sorting;

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