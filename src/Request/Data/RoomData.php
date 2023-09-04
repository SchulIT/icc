<?php

namespace App\Request\Data;

use App\Validator\NotAResource;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[NotAResource]
class RoomData {

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $name = null;

    #[Serializer\Type('string')]
    private ?string $description = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Serializer\Type('int')]
    private ?int $capacity = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): RoomData {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): RoomData {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

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
     */
    public function setCapacity(?int $capacity): RoomData {
        $this->capacity = $capacity;
        return $this;
    }
}