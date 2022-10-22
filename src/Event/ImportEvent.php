<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ImportEvent extends Event {
    public function __construct(private array $added, private array $updated, private array $removed)
    {
    }

    public function getAdded(): array {
        return $this->added;
    }

    public function getUpdated(): array {
        return $this->updated;
    }

    public function getRemoved(): array {
        return $this->removed;
    }
}