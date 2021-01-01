<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="resource")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="class", type="string")
 * @ORM\DiscriminatorMap({
 *      "resource"="ResourceEntity",
 *      "room"="Room"
 * })
 * @UniqueEntity(fields={"name"})
 * @Auditable()
 */
class ResourceEntity {

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
     * @return ResourceEntity $this
     */
    public function setName(?string $name): ResourceEntity {
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
     * @return ResourceEntity $this
     */
    public function setDescription(?string $description): ResourceEntity {
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
     * @return ResourceEntity
     */
    public function setIsReservationEnabled(bool $isReservationEnabled): ResourceEntity {
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
     * @return ResourceEntity
     */
    public function setType(?ResourceType $type): ResourceEntity {
        $this->type = $type;
        return $this;
    }

    public function __toString(): ?string {
        return $this->getName();
    }
}