<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetablePeriodData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @Serializer\Type("datetime")
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $start;

    /**
     * @Serializer\Type("datetime")
     * @Assert\NotNull()
     * @Assert\GreaterThan(propertyPath="start")
     * @var \DateTime
     */
    private $end;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return TimetablePeriodData
     */
    public function setId(?string $id): TimetablePeriodData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return TimetablePeriodData
     */
    public function setName(?string $name): TimetablePeriodData {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?\DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime|null $start
     * @return TimetablePeriodData
     */
    public function setStart(?\DateTime $start): TimetablePeriodData {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime|null $end
     * @return TimetablePeriodData
     */
    public function setEnd(?\DateTime $end): TimetablePeriodData {
        $this->end = $end;
        return $this;
    }
}