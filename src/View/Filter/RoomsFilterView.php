<?php

namespace App\View\Filter;

use App\Entity\Room;

class RoomsFilterView {
    /** @var Room[] */
    private $rooms;

    /** @var Room[] */
    private $currentRooms;

    /**
     * @param Room[] $rooms
     * @param Room[] $currentRooms
     */
    public function __construct(array $rooms, array $currentRooms) {
        $this->rooms = $rooms;
        $this->currentRooms = $currentRooms;
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