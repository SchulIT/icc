<?php

namespace App\Exception;

class UnexpectedTypeException extends \Exception {

    /**
     * @param mixed $value
     * @param string|string[] $expectedType
     */
    public function __construct($value, $expectedType) {
        if(!is_array($expectedType)) {
            $expectedType = [ $expectedType ];
        }

        parent::__construct(
            sprintf('Expected argument of type "%s", "%s" given', implode('" or "', $expectedType), \is_object($value) ? \get_class($value) : \gettype($value))
        );
    }
}