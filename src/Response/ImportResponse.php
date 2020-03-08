<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * Indicates a successful import. For now, only the number of added, updated and removed entities is serialized to JSON.
 */
class ImportResponse {

    /**
     * Added entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $added;

    /**
     * Updated entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $updated;

    /**
     * Removed entities.
     * @Serializer\Exclude()
     * @var object[]
     */
    private $removed;

    /**
     * Number of added entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $addedCount;

    /**
     * Number of updated entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $updatedCount;

    /**
     * Number of removed entities.
     *
     * @Serializer\Type("int")
     * @var int
     */
    private $removedCount;

    public function __construct(array $added, array $updated, array $removed) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;

        $this->addedCount = count($added);
        $this->updatedCount = count($updated);
        $this->removedCount = count($removed);
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

}