<?php

namespace App\Framework\Import\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template T
 */
abstract class RemoveEvent extends Event {

    /**
     * @param object|T $entity
     * @param bool $preventRemoval
     */
    public function __construct(
        public readonly object $entity,
        public bool $preventRemoval = false
    ) {

    }
}
