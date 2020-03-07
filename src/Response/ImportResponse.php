<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("int")
     * @var int
     */
    private $addedCount;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $updatedCount;

    /**
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