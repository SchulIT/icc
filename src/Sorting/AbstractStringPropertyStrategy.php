<?php

namespace App\Sorting;

abstract class AbstractStringPropertyStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $stringStrategy) {
        $this->stringStrategy = $stringStrategy;
    }

    abstract  protected function getValue($object): string;

    public final function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare(
            $this->getValue($objectA),
            $this->getValue($objectB)
        );
    }
}