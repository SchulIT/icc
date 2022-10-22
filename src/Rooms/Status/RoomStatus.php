<?php

namespace App\Rooms\Status;

use App\Entity\Room;

class RoomStatus {

    private ?string $name = null;

    private ?string $link = null;

    /** @var RoomStatusBadge[] */
    private array $badges = [ ];

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): RoomStatus {
        $this->name = $name;
        return $this;
    }

    public function getLink(): ?string {
        return $this->link;
    }

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

    public function addBadge(RoomStatusBadge $badge): RoomStatus {
        $this->badges[] = $badge;
        return $this;
    }
}