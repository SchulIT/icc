<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use App\Entity\AppointmentCategory as AppointmentCategoryEntity;

class AppointmentCategory {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private ?string $name = null;

    /**
     * HTML Hex-Color, e.g. #AB00A1 (case might be mixed)
     *
     * @Serializer\SerializedName("color")
     * @Serializer\Type("string")
     */
    private ?string $color = null;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): AppointmentCategory {
        $this->name = $name;
        return $this;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function setColor(string $color): AppointmentCategory {
        $this->color = $color;
        return $this;
    }

    public static function fromEntity(AppointmentCategoryEntity $entity): self {
        return (new self())
            ->setUuid($entity->getUuid())
            ->setName($entity->getName())
            ->setColor($entity->getColor());
    }
}