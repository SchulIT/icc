<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ImportResponse {

    /**
     * Added entities.
     * @Serializer\Type("array<object>")
     * @var object[]
     */
    private $added;

    /**
     * Updated entities.
     * @Serializer\Type("array<object>")
     * @var object[]
     */
    private $updated;

    /**
     * Removed entities.
     * @Serializer\Type("array<object>")
     * @var object[]
     */
    private $removed;

    public function __construct(array $added, array $updated, array $removed) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
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