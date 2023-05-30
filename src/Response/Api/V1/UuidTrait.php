<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @var UuidInterface
     */
    #[Serializer\SerializedName('uuid')]
    #[Serializer\ReadOnly]
    #[Serializer\Type('string')]
    #[Serializer\Accessor(getter: 'getUuidString')]
    private $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): self {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuidString(): string {
        return (string)$this->uuid;
    }
}