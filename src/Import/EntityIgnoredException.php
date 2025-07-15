<?php

namespace App\Import;

use Exception;
use Throwable;

class EntityIgnoredException extends Exception {
    public function __construct(private $entity, $message = "", $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getEntity() {
        return $this->entity;
    }
}