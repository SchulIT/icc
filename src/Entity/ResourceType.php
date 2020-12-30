<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ResourceType {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity="ResourceEntity", mappedBy="type", fetch="EXTRA_LAZY")
     * @var Collection<ResourceEntity>
     */
    private $resources;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->resources = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return ResourceType
     */
    public function setName(?string $name): ResourceType {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return ResourceType
     */
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
}