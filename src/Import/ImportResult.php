<?php

namespace App\Import;

class ImportResult {

    public function __construct(private array $added, private array $updated, private array $removed, private array $ignored, private object $request)
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

    public function getIgnored(): array {
        return $this->ignored;
    }

    public function getRequest(): object {
        return $this->request;
    }
}