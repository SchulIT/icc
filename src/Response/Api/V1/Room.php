<?php

namespace App\Response\Api\V1;

use App\Entity\Room as RoomEntity;
use JMS\Serializer\Annotation as Serializer;

class Room {

    use UuidTrait;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     */
    private ?string $name = null;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Room {
        $this->name = $name;
        return $this;
    }

    public static function fromEntity(RoomEntity $entity): self {
        return (new self())
            ->setUuid($entity->getUuid())
            ->setName($entity->getName());
    }

}