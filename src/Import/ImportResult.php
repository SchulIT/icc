<?php

namespace App\Import;

class ImportResult {

    /** @var array */
    private $added;

    /** @var array */
    private $updated;

    /** @var array */
    private $removed;

    /** @var array */
    private $ignored;

    public function __construct(array $added, array $updated, array $removed, array $ignored) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
        $this->ignored = $ignored;
    }

    /**
     * @return array
     */
    public function getAdded(): array {
        return $this->added;
    }

    /**
     * @return array
     */
    public function getUpdated(): array {
        return $this->updated;
    }

    /**
     * @return array
     */
    public function getRemoved(): array {
        return $this->removed;
    }

    /**
     * @return array
     */
    public function getIgnored(): array {
        return $this->ignored;
    }
}