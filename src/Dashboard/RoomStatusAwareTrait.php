<?php

namespace App\Dashboard;

use App\Room\Status\RoomStatus;

trait RoomStatusAwareTrait {

    /** @var RoomStatus[] */
    private array $roomStatus = [ ];

    public function getRoomStatus(): array {
        return $this->roomStatus;
    }

    public function setRoomStatus(array $roomStatus): void {
        $this->roomStatus = $roomStatus;
    }
}
