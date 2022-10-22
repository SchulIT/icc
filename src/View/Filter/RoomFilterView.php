<?php

namespace App\View\Filter;

use App\Entity\Room;

class RoomFilterView implements FilterViewInterface {

    /**
     * @param Room[] $rooms
     */
    public function __construct(private array $rooms, private ?Room $currentRoom)
    {
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    public function getCurrentRoom(): ?Room {
        return $this->currentRoom;
    }

    public function isEnabled(): bool {
        return count($this->rooms) > 0;
    }
}