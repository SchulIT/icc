<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class ResourceType implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $icon = null;

    /**
     * @var Collection<ResourceEntity>
     */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: ResourceEntity::class, fetch: 'EXTRA_LAZY')]
    private $resources;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->resources = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): ResourceType {
        $this->name = $name;
        return $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): ResourceType {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getResources() {
        return $this->resources;
    }

    public function __toString(): string {
        return (string) $this->getName();
    }
}