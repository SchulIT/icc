<?php

namespace App\Rooms\Status;

interface StatusHelperInterface {
    public function getStatus(string $room): ?RoomStatus;
}