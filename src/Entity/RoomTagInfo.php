<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="room_roomtags")
 */
class RoomTagInfo {
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="tags")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $room;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="RoomTag")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $tag;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $value;

    /**
     * @return Room
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return RoomTagInfo $this
     */
    public function setRoom(Room $room) {
        $this->room = $room;
        return $this;
    }

    /**
     * @return RoomTag
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param RoomTag $tag
     * @return RoomTagInfo $this
     */
    public function setTag(RoomTag $tag) {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return integer
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param integer $value
     * @return RoomTagInfo $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

}