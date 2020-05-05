<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Room {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $externalId;

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
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $capacity;

    /**
     * @ORM\OneToMany(targetEntity="RoomTagInfo", mappedBy="room", cascade={"persist"})
     * @var Collection
     */
    private $tags;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return null|int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Room
     */
    public function setExternalId(?string $externalId): Room {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Room $this
     */
    public function setName($name) {
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
     * @return Room $this
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCapacity() {
        return $this->capacity;
    }

    /**
     * @param int|null $capacity
     * @return Room $this
     */
    public function setCapacity($capacity) {
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * @param RoomTagInfo $tagInfo
     */
    public function addTag(RoomTagInfo $tagInfo) {
        $this->tags->add($tagInfo);
    }

    /**
     * @param RoomTagInfo $tagInfo
     */
    public function removeTag(RoomTagInfo $tagInfo) {
        $this->tags->removeElement($tagInfo);
    }

    /**
     * @return Collection<RoomTagInfo>
     */
    public function getTags(): Collection {
        return $this->tags;
    }
}