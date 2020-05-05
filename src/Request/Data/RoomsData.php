<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @Serializer\Type("array<App\Request\Data\RoomData>")
     * @Serializer\SerializedName("rooms")
     * @Assert\Valid()
     * @var RoomData[]
     */
    private $rooms = [ ];

    /**
     * @return RoomData[]
     */
    public function getRooms() {
        return $this->rooms;
    }
}