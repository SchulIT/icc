<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @Serializer\SerializedName("uuid")
     * @Serializer\ReadOnly()
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getUuidString")
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    /**
     * @param UuidInterface $uuid
     * @return UuidTrait
     */
    public function setUuid(UuidInterface $uuid) {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuidString(): string {
        return (string)$this->uuid;
    }
}