<?php

namespace App\Rooms\Status;

use App\Entity\Room;

class RoomStatus {

    /** @var string */
    private $name;

    /** @var string|null */
    private $link;

    /** @var RoomStatusBadge[] */
    private $badges = [ ];

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RoomStatus
     */
    public function setName(string $name): RoomStatus {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return RoomStatus
     */
    public function setLink(?string $link): RoomStatus {
        $this->link = $link;
        return $this;
    }

    /**
     * @return RoomStatusBadge[]
     */
    public function getBadges(): array {
        return $this->badges;
    }

    /**
     * @param RoomStatusBadge $badge
     * @return RoomStatus
     */
    public function addBadge(RoomStatusBadge $badge): RoomStatus {
        $this->badges[] = $badge;
        return $this;
    }
}