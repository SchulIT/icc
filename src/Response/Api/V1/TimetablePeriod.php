<?php

namespace App\Response\Api\V1;

use App\Entity\TimetablePeriod as TimetablePeriodEntity;
use DateTime;
use JMS\Serializer\Annotation as Serializer;

class TimetablePeriod {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     * @var string
     */
    private $name;

    /**
     * @Serializer\SerializedName("start")
     * @Serializer\Type("DateTime")
     * @var DateTime
     */
    private $start;

    /**
     * @Serializer\SerializedName("end")
     * @Serializer\Type("DateTime")
     * @var DateTime
     */
    private $end;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TimetablePeriod
     */
    public function setName(string $name): TimetablePeriod {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return TimetablePeriod
     */
    public function setStart(DateTime $start): TimetablePeriod {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return TimetablePeriod
     */
    public function setEnd(DateTime $end): TimetablePeriod {
        $this->end = $end;
        return $this;
    }

    public static function fromEntity(TimetablePeriodEntity $entity): self {
        return (new static())
            ->setUuid($entity->getUuid())
            ->setName($entity->getName())
            ->setStart($entity->getStart())
            ->setEnd($entity->getEnd());
    }
}