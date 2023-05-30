<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * Indicates a successful import. For now, only the number of added, updated and removed entities is serialized to JSON.
 */
class ImportResponse {

    /**
     * Number of added entities.
     */
    #[Serializer\Type('int')]
    private int $addedCount;

    /**
     * Number of updated entities.
     */
    #[Serializer\Type('int')]
    private int $updatedCount;

    /**
     * Number of removed entities.
     */
    #[Serializer\Type('int')]
    private int $removedCount;

    /**
     * Number of ignored entities.
     */
    #[Serializer\Type('int')]
    private int $ignoredCount;

    /**
     * @param object[] $added
     * @param object[] $updated
     * @param object[] $removed
     * @param object[] $ignored
     */
    public function __construct(/**
     * Added entities.
     */
    #[Serializer\Exclude]
    private array $added, /**
     * Updated entities.
     */
    #[Serializer\Exclude]
    private array $updated, /**
     * Removed entities.
     */
    #[Serializer\Exclude]
    private array $removed, /**
     * Ignored entities.
     */
    private array $ignored) {
        $this->addedCount = count($added);
        $this->updatedCount = count($updated);
        $this->removedCount = count($removed);
        $this->ignoredCount = count($ignored);
    }

    /**
     * @return object[]
     */
    public function getAdded() {
        return $this->added;
    }

    /**
     * @return object[]
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @return object[]
     */
    public function getRemoved() {
        return $this->removed;
    }

    /**
     * @return object[]
     */
    public function getIgnored(): array {
        return $this->ignored;
    }

    public function getAddedCount(): int {
        return $this->addedCount;
    }

    public function getUpdatedCount(): int {
        return $this->updatedCount;
    }

    public function getRemovedCount(): int {
        return $this->removedCount;
    }

    public function getIgnoredCount(): int {
        return $this->ignoredCount;
    }
}