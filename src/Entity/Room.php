<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class Room extends ResourceEntity {

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    private $capacity;

    /**
     * @ORM\OneToMany(targetEntity="RoomTagInfo", mappedBy="room", cascade={"persist"}, orphanRemoval=true)
     * @var Collection<RoomTagInfo>
     */
    private $tags;

    public function __construct() {
        parent::__construct();

        $this->tags = new ArrayCollection();
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

    public function ensureAllTagsHaveRoomAssociated(): void {
        /** @var RoomTagInfo $tag */
        foreach($this->getTags() as $tag) {
            $tag->setRoom($this);
        }
    }

}