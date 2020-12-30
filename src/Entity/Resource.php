<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="class", type="string")
 * @ORM\DiscriminatorMap({
 *      "resource"="Resource",
 *      "room"="Room"
 * })
 * @UniqueEntity(fields={"name"})
 */
class Resource {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     * @Assert\NotNull()
     * @Assert\Length(max="16")
     */
    private $name;

    /**
     * @ORM\Column(type="text", name="`description`", nullable=true)
     * @NullOrNotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isReservationEnabled = true;

    /**
     * @ORM\ManyToOne(targetEntity="ResourceType", inversedBy="resources")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     * @Assert\NotNull
     * @var ResourceType|null
     */
    private $type;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Resource $this
     */
    public function setName(?string $name): Resource {
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
     * @return Resource $this
     */
    public function setDescription(?string $description): Resource {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReservationEnabled(): bool {
        return $this->isReservationEnabled;
    }

    /**
     * @param bool $isReservationEnabled
     * @return Resource
     */
    public function setIsReservationEnabled(bool $isReservationEnabled): Resource {
        $this->isReservationEnabled = $isReservationEnabled;
        return $this;
    }

    /**
     * @return ResourceType|null
     */
    public function getType(): ?ResourceType {
        return $this->type;
    }

    /**
     * @param ResourceType|null $type
     * @return Resource
     */
    public function setType(?ResourceType $type): Resource {
        $this->type = $type;
        return $this;
    }

    public function __toString(): ?string {
        return $this->getName();
    }
}