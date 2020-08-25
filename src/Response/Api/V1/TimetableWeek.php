<?php

namespace App\Response\Api\V1;

use App\Entity\TimetableWeek as TimetableWeekEntity;
use JMS\Serializer\Annotation as Serializer;

class TimetableWeek {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     * @var string
     */
    private $name;

    /**
     * @Serializer\SerializedName("week_mod")
     * @Serializer\Type("int")
     * @var int
     */
    private $weekMod;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TimetableWeek
     */
    public function setName(string $name): TimetableWeek {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeekMod(): int {
        return $this->weekMod;
    }

    /**
     * @param int $weekMod
     * @return TimetableWeek
     */
    public function setWeekMod(int $weekMod): TimetableWeek {
        $this->weekMod = $weekMod;
        return $this;
    }

    public static function fromEntity(TimetableWeekEntity $entity): self {
        return (new self())
            ->setUuid($entity->getUuid())
            ->setName($entity->getDisplayName())
            ->setWeekMod($entity->getWeekMod());
    }
}