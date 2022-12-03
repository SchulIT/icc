<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\Table(name: 'resource')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
#[ORM\DiscriminatorMap([
    'resource' => ResourceEntity::class,
    'room' => Room::class
])]
class ResourceEntity implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotNull]
    #[Assert\Length(max: 16)]
    #[ORM\Column(type: 'string', length: 16, unique: true)]
    private $name;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private bool $isReservationEnabled = true;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: ResourceType::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ResourceType $type = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @return ResourceEntity $this
     */
    public function setName(?string $name): ResourceEntity {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @return ResourceEntity $this
     */
    public function setDescription(?string $description): ResourceEntity {
        $this->description = $description;
        return $this;
    }

    public function isReservationEnabled(): bool {
        return $this->isReservationEnabled;
    }

    public function setIsReservationEnabled(bool $isReservationEnabled): ResourceEntity {
        $this->isReservationEnabled = $isReservationEnabled;
        return $this;
    }

    public function getType(): ?ResourceType {
        return $this->type;
    }

    public function setType(?ResourceType $type): ResourceEntity {
        $this->type = $type;
        return $this;
    }

    public function __toString(): string {
        return $this->getName();
    }
}