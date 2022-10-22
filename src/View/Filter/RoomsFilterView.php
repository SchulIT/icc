<?php

namespace App\View\Filter;

use App\Entity\Room;

class RoomsFilterView {
    /**
     * @param Room[] $rooms
     * @param Room[] $currentRooms
     */
    public function __construct(private array $rooms, private array $currentRooms)
    {
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @return Room[]
     */
    public function getCurrentRooms(): array {
        return $this->currentRooms;
    }

    public function isEnabled(): bool {
        return count($this->rooms) > 0;
    }
}