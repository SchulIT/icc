<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @Serializer\Type("array<App\Request\Data\RoomData>")
     * @Serializer\SerializedName("rooms")
     * @UniqueId(propertyPath="id")
     * @var RoomData[]
     */
    #[Assert\Valid]
    private array $rooms = [ ];

    /**
     * @return RoomData[]
     */
    public function getRooms() {
        return $this->rooms;
    }

    /**
     * @param RoomData[] $rooms
     */
    public function setRooms(array $rooms): RoomsData {
        $this->rooms = $rooms;
        return $this;
    }
}