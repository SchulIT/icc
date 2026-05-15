<?php

namespace App\Room\Status;

interface StatusHelperInterface {
    public function getStatus(string $room): ?RoomStatus;
}