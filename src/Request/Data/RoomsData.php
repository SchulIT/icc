<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @Serializer\Type("array<App\Request\Data\RoomData>")
     * @Assert\Valid()
     * @var RoomData[]
     */
    private $rooms;

    /**
     * @return RoomData[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }

    /**
     * @param RoomData[] $rooms
     * @return RoomsData
     */
    public function setRooms(array $rooms): RoomsData {
        $this->rooms = $rooms;
        return $this;
    }
}