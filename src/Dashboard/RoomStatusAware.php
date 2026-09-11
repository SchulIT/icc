<?php

namespace App\Dashboard;

use App\Room\Status\RoomStatus;

interface RoomStatusAware {

    /**
     * @return RoomStatus[]
     */
    public function getRoomStatus(): array;

    /**
     * @param RoomStatus[] $roomStatus
     * @return void
     */
    public function setRoomStatus(array $roomStatus): void;
}
