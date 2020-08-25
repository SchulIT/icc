<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use App\Entity\AppointmentCategory as AppointmentCategoryEntity;

class AppointmentCategory {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     * @var string
     */
    private $name;

    /**
     * HTML Hex-Color, e.g. #AB00A1 (case might be mixed)
     *
     * @Serializer\SerializedName("color")
     * @Serializer\Type("string")
     * @var string
     */
    private $color;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AppointmentCategory
     */
    public function setName(string $name): AppointmentCategory {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @param string $color
     * @return AppointmentCategory
     */
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