<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Room;
use App\Framework\View\Filter\FilterViewInterface;

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