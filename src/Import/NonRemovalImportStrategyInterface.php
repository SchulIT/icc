<?php

namespace App\Import;

/**
 * Adds ability to prevent removal of non imported entities.
 */
interface NonRemovalImportStrategyInterface {
    /**
     * @param object $data
     */
    public function preventRemoval($data): bool;
}