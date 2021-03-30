<?php

namespace App\Import;

/**
 * Adds ability to prevent removal of non imported entities.
 */
interface NonRemovalImportStrategyInterface {
    /**
     * @param object $data
     * @return bool
     */
    public function preventRemoval($data): bool;
}