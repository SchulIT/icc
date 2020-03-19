<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    private $longName;

    /**
     * @Serializer\Type("int")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $capacity;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return RoomData
     */
    public function setId(?string $id): RoomData {
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
     * @return RoomData
     */
    public function setName(?string $name): RoomData {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLongName(): ?string {
        return $this->longName;
    }

    /**
     * @param string|null $longName
     * @return RoomData
     */
    public function setLongName(?string $longName): RoomData {
        $this->longName = $longName;
        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity(): int {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return RoomData
     */
    public function setCapacity(int $capacity): RoomData {
        $this->capacity = $capacity;
        return $this;
    }
}