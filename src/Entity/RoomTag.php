<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class RoomTag {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(max="64")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasValue = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $icons;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RoomTag $this
     */
    public function setName($name): RoomTag {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return RoomTag $this
     */
    public function setDescription($description): RoomTag {
        $this->description = $description;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasValue() {
        return $this->hasValue;
    }

    /**
     * @param boolean $hasValue
     * @return RoomTag $this
     */
    public function setHasValue(bool $hasValue): RoomTag {
        $this->hasValue = $hasValue;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcons(): ?string {
        return $this->icons;
    }

    /**
     * @param string|null $icons
     * @return RoomTag
     */
    public function setIcons(?string $icons): RoomTag {
        $this->icons = $icons;
        return $this;
    }
}