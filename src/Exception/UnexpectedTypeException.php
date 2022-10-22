<?php

namespace App\Exception;

use Exception;
class UnexpectedTypeException extends Exception {

    /**
     * @param string|string[] $expectedType
     */
    public function __construct(mixed $value, $expectedType) {
        if(!is_array($expectedType)) {
            $expectedType = [ $expectedType ];
        }

        parent::__construct(
            sprintf('Expected argument of type "%s", "%s" given', implode('" or "', $expectedType), get_debug_type($value))
        );
    }
}