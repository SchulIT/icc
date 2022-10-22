<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class ResourceType implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $icon = null;

    /**
     * @ORM\OneToMany(targetEntity="ResourceEntity", mappedBy="type", fetch="EXTRA_LAZY")
     * @var Collection<ResourceEntity>
     */
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