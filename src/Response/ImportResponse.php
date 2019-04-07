<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ImportResponse {

    /**
     * Added entities.
     * @Serializer\Type("array")
     */
    private $added;

    /**
     * Updated entities.
     * @Serializer\Type("array")
     */
    private $updated;

    /**
     * Removed entities.
     * @Serializer\Type("array")
     */
    private $removed;

    public function __construct(array $added, array $updated, array $removed) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
    }

    /**
     * @return mixed
     */
    public function getAdded() {
        return $this->added;
    }

    /**
     * @return mixed
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @return mixed
     */
    public function getRemoved() {
        return $this->removed;
    }

}