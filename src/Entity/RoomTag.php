<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class RoomTag {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: 'string', length: 64)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private bool $hasValue = false;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $icons = null;

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
     * @return RoomTag $this
     */
    public function setHasValue(bool $hasValue): RoomTag {
        $this->hasValue = $hasValue;
        return $this;
    }

    public function getIcons(): ?string {
        return $this->icons;
    }

    public function setIcons(?string $icons): RoomTag {
        $this->icons = $icons;
        return $this;
    }
}