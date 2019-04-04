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

    /**
     * @return mixed
     */
    public function getAdded() {
        return $this->added;
    }

    /**
     * @param mixed $added
     * @return ImportResponse
     */
    public function setAdded($added) {
        $this->added = $added;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return ImportResponse
     */
    public function setUpdated($updated) {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemoved() {
        return $this->removed;
    }

    /**
     * @param mixed $removed
     * @return ImportResponse
     */
    public function setRemoved($removed) {
        $this->removed = $removed;
        return $this;
    }
}