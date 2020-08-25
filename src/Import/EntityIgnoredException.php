<?php

namespace App\Import;

use Exception;
use Throwable;

class EntityIgnoredException extends Exception {
    private $entity;

    public function __construct($entity, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->entity = $entity;
    }

    public function getEntity() {
        return $this->entity;
    }
}