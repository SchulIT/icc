<?php

namespace App\View\Filter;

use App\Entity\Room;

class RoomFilterView {

    /** @var Room[] */
    private $rooms;

    /** @var Room|null */
    private $currentRoom;

    /**
     * @param Room[] $rooms
     * @param Room|null $room
     */
    public function __construct(array $rooms, ?Room $room) {
        $this->rooms = $rooms;
        $this->currentRoom = $room;
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @return Room|null
     */
    public function getCurrentRoom(): ?Room {
        return $this->currentRoom;
    }
}