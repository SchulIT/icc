<?php

namespace App\Import;

class ImportResult {

    /** @var array */
    private $added;

    /** @var array */
    private $updated;

    /** @var array */
    private $removed;

    public function __construct(array $added, array $updated, array $removed) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
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
}