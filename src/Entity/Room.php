<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class Room extends ResourceEntity {

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $externalId = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $capacity;

    /**
     * @var Collection<RoomTagInfo>
     */
    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomTagInfo::class, cascade: ['persist'], orphanRemoval: true)]
    private $tags;

    public function __construct() {
        parent::__construct();

        $this->tags = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

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

    public function addTag(RoomTagInfo $tagInfo) {
        $this->tags->add($tagInfo);
    }

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