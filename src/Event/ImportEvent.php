<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ImportEvent extends Event {
    private $added;
    private $updated;
    private $removed;

    public function __construct(array $added, array $updated, array $removed) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
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