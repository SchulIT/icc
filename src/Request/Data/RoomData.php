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
    private $description;

    /**
     * @Serializer\Type("int")
     * @Assert\NotBlank(allowNull=true)
     * @Assert\GreaterThanOrEqual(0)
     * @var int|null
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
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return RoomData
     */
    public function setDescription(?string $description): RoomData {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity(): ?int {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return RoomData
     */
    public function setCapacity(?int $capacity): RoomData {
        $this->capacity = $capacity;
        return $this;
    }
}